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

    // Nova variável para a pesquisa interna do modal
    public string $detailSearch = '';

    public bool $isDetailsModalOpen = false;

    public function layoutData()
    {
        return ['header' => 'Vistos em Relatórios Mensais'];
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    // Função engatilhada automaticamente quando o fiscal digita na pesquisa do modal
    public function updatedDetailSearch()
    {
        $this->loadDetails();
    }

    public function render()
    {
        $query = ReportSubmission::with(['guardUser', 'fiscal', 'vehicle' => fn($q) => $q->withTrashed(), 'assignedFiscal'])
            ->where('status', $this->activeTab);

        $user = auth()->user();

        if ($user->role !== 'admin') {
            if ($user->fiscal_type === 'official') {
                $query->where('type', 'official');
            } elseif ($user->fiscal_type === 'private') {
                $query->where('type', 'private');
            }
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
        $this->selectedSubmission = ReportSubmission::with(['guardUser', 'fiscal'])->findOrFail($id);
        $this->detailSearch = ''; // Limpa a pesquisa ao abrir um novo relatório
        $this->loadDetails();

        $this->isDetailsModalOpen = true;
    }

    // Isolamos o carregamento de detalhes para que a pesquisa possa reutilizá-lo
    public function loadDetails()
    {
        if (!$this->selectedSubmission) {
            $this->details = [];
            return;
        }

        $id = $this->selectedSubmission->id;
        $search = trim($this->detailSearch);

        if ($this->selectedSubmission->type === 'private') {
            $query = PrivateEntry::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                ->where('report_submission_id', $id);

            // Filtro de pesquisa de Particulares
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', fn($v) => $v->where('license_plate', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"))
                        ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%{$search}%"))
                        ->orWhere('entry_reason', 'like', "%{$search}%");
                });
            }

            $this->details = $query->orderBy('entry_at', 'asc')->get();
        } else {
            $query = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                ->where('report_submission_id', $id);

            // Filtro de pesquisa de Oficiais
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', fn($v) => $v->where('license_plate', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"))
                        ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%{$search}%"))
                        ->orWhere('destination', 'like', "%{$search}%");
                });
            }

            $this->details = $query->orderBy('departure_datetime', 'asc')->get();
        }
    }

    public function closeDetailsModal()
    {
        $this->isDetailsModalOpen = false;
        $this->reset(['selectedSubmission', 'details', 'detailSearch']);
    }

    public function approve(int $id)
    {
        $submission = ReportSubmission::findOrFail($id);

        $submission->update([
            'fiscal_id'          => Auth::id(),
            'assigned_fiscal_id' => Auth::id(),
            'approved_at'        => now(),
            'status'             => 'approved',
        ]);

        session()->flash('success', 'Visto registrado com sucesso! O relatório foi arquivado.');
        $this->isDetailsModalOpen = false;
    }
}
