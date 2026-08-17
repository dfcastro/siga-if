<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ReportSubmission;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class FiscalApproval extends Component
{
    use WithPagination;

    public string $activeTab = 'pending';
    public ?ReportSubmission $selectedSubmission = null;
    public $details = [];

    // Pesquisa interna do modal de análise.
    public string $detailSearch = '';

    public bool $isDetailsModalOpen = false;

    // Confirmação explícita antes de registrar o visto.
    public bool $isApprovalConfirmationOpen = false;
    public ?int $submissionPendingApprovalId = null;

    public function layoutData()
    {
        return ['header' => 'Vistos em Relatórios Mensais'];
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
        $this->resetApprovalState();
        $this->isDetailsModalOpen = false;
        $this->reset(['selectedSubmission', 'details', 'detailSearch']);
    }

    public function updatedDetailSearch()
    {
        $this->loadDetails();
    }

    public function render()
    {
        $query = ReportSubmission::with([
            'guardUser',
            'fiscal',
            'vehicle' => fn ($q) => $q->withTrashed(),
            'assignedFiscal',
        ])->where('status', $this->activeTab);

        $user = auth()->user();

        // Mesmo que a rota/middleware seja alterada no futuro, o componente não
        // deve expor relatórios para perfis que não podem fiscalizá-los.
        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'official') {
                $query->where('type', 'official');
            } elseif ($user->fiscal_type === 'private') {
                $query->where('type', 'private');
            } elseif ($user->fiscal_type !== 'both') {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role !== 'admin') {
            $query->whereRaw('1 = 0');
        }

        if ($this->activeTab === 'approved') {
            $query->orderBy('approved_at', 'desc');
        } else {
            $query->orderBy('submitted_at', 'asc');
        }

        $submissions = $query->paginate(10);

        return view('livewire.fiscal-approval', [
            'submissions' => $submissions,
        ]);
    }

    public function viewDetails(int $id)
    {
        $submission = ReportSubmission::with([
            'guardUser',
            'fiscal',
            'vehicle' => fn ($q) => $q->withTrashed(),
            'assignedFiscal',
        ])->findOrFail($id);

        abort_unless($this->canReview($submission), 403);

        $this->selectedSubmission = $submission;
        $this->detailSearch = '';
        $this->loadDetails();
        $this->resetApprovalState();
        $this->isDetailsModalOpen = true;
    }

    public function loadDetails()
    {
        if (!$this->selectedSubmission) {
            $this->details = [];
            return;
        }

        $id = $this->selectedSubmission->id;
        $search = trim($this->detailSearch);

        if ($this->selectedSubmission->type === 'private') {
            $query = PrivateEntry::with([
                'vehicle' => fn ($q) => $q->withTrashed(),
                'driver' => fn ($q) => $q->withTrashed(),
            ])->where('report_submission_id', $id);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', fn ($v) => $v->where('license_plate', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%"))
                        ->orWhereHas('driver', fn ($d) => $d->where('name', 'like', "%{$search}%"))
                        ->orWhere('entry_reason', 'like', "%{$search}%");
                });
            }

            $this->details = $query->orderBy('entry_at', 'asc')->get();
        } else {
            $query = OfficialTrip::with([
                'vehicle' => fn ($q) => $q->withTrashed(),
                'driver' => fn ($q) => $q->withTrashed(),
            ])->where('report_submission_id', $id);

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', fn ($v) => $v->where('license_plate', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%"))
                        ->orWhereHas('driver', fn ($d) => $d->where('name', 'like', "%{$search}%"))
                        ->orWhere('destination', 'like', "%{$search}%");
                });
            }

            $this->details = $query->orderBy('departure_datetime', 'asc')->get();
        }
    }

    public function closeDetailsModal()
    {
        $this->isDetailsModalOpen = false;
        $this->resetApprovalState();
        $this->reset(['selectedSubmission', 'details', 'detailSearch']);
    }

    /**
     * Abre a segunda etapa do fluxo: confirmação do visto.
     * A ação só pode ser iniciada a partir do relatório que está aberto para análise.
     */
    public function requestApproval(int $id)
    {
        if (!$this->isDetailsModalOpen || !$this->selectedSubmission || $this->selectedSubmission->id !== $id) {
            session()->flash('error', 'Abra e analise o relatório antes de registrar o visto.');
            return;
        }

        $submission = ReportSubmission::with([
            'guardUser',
            'vehicle' => fn ($q) => $q->withTrashed(),
        ])->findOrFail($id);

        abort_unless($this->canReview($submission), 403);

        if ($submission->status !== 'pending') {
            session()->flash('error', 'Este relatório não está mais aguardando visto. Atualize a página e tente novamente.');
            $this->closeDetailsModal();
            return;
        }

        // Atualiza os dados usados no resumo da confirmação.
        $this->selectedSubmission = $submission;
        $this->submissionPendingApprovalId = $submission->id;

        // O x-modal de detalhes usa z-index superior ao confirmation-dialog.
        // Fechamos apenas visualmente o detalhe e preservamos os dados para a confirmação.
        $this->isDetailsModalOpen = false;
        $this->isApprovalConfirmationOpen = true;
    }

    public function cancelApproval()
    {
        $this->isApprovalConfirmationOpen = false;
        $this->submissionPendingApprovalId = null;

        // Volta para o relatório que estava sendo analisado.
        if ($this->selectedSubmission) {
            $this->isDetailsModalOpen = true;
        }
    }

    public function confirmApproval()
    {
        if (!$this->isApprovalConfirmationOpen || !$this->submissionPendingApprovalId) {
            session()->flash('error', 'Nenhum relatório foi selecionado para receber o visto.');
            return;
        }

        $submission = ReportSubmission::findOrFail($this->submissionPendingApprovalId);

        abort_unless($this->canReview($submission), 403);

        // Revalida o estado no momento da confirmação para evitar clique duplo
        // ou aprovação concorrente de um relatório já processado.
        if ($submission->status !== 'pending') {
            $this->isApprovalConfirmationOpen = false;
            $this->submissionPendingApprovalId = null;
            $this->isDetailsModalOpen = false;
            $this->reset(['selectedSubmission', 'details', 'detailSearch']);

            session()->flash('error', 'Este relatório já foi processado e não pode receber um novo visto.');
            return;
        }

        $submission->update([
            'fiscal_id'          => Auth::id(),
            'assigned_fiscal_id' => Auth::id(),
            'approved_at'        => now(),
            'status'             => 'approved',
        ]);

        $this->isApprovalConfirmationOpen = false;
        $this->submissionPendingApprovalId = null;
        $this->isDetailsModalOpen = false;
        $this->reset(['selectedSubmission', 'details', 'detailSearch']);

        session()->flash('success', 'Visto registrado com sucesso! O relatório foi arquivado.');
    }

    private function resetApprovalState(): void
    {
        $this->isApprovalConfirmationOpen = false;
        $this->submissionPendingApprovalId = null;
    }

    private function canReview(ReportSubmission $submission): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role !== 'fiscal') {
            return false;
        }

        return match ($user->fiscal_type) {
            'both' => true,
            'private' => $submission->type === 'private',
            'official' => $submission->type === 'official',
            default => false,
        };
    }
}
