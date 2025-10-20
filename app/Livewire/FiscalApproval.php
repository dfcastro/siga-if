<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ReportSubmission;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class FiscalApproval extends Component
{
    use WithPagination;

    public ?ReportSubmission $selectedSubmission = null;
    public Collection $submissionEntries;
    public bool $showDetailsModal = false;
    public string $filterStatus = 'pending';
    public int $totalDistance = 0;
    public string $typeFilter = '';

    public function layoutData()
    {
        return ['header' => 'Aprovação e Arquivo de Relatórios'];
    }

    public function setFilter(string $status)
    {
        $this->filterStatus = $status;
        $this->resetPage(); // Reseta a paginação ao mudar de aba
    }

    public function setTypeFilter(string $type)
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    public function render()
    {
        $query = ReportSubmission::with(['guardUser', 'fiscal', 'vehicle' => fn($q) => $q->withTrashed()]) // Adiciona o 'vehicle' para a tabela principal
            ->where('status', $this->filterStatus);

        $user = auth()->user();

        if ($user->role !== 'admin') {
            if ($user->fiscal_type === 'official') {
                $query->where('type', 'official');
            } elseif ($user->fiscal_type === 'private') {
                $query->where('type', 'private');
            }
        }

        // Aplica o filtro de tipo da interface, se um for selecionado
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }
        // A sua lógica de ordenação permanece a mesma
        if ($this->filterStatus === 'approved') {
            $query->orderBy('approved_at', 'desc');
        } else {
            // Ordena os pendentes pelos mais antigos primeiro
            $query->orderBy('created_at', 'asc');
        }

        $submissions = $query->paginate(10);

        return view('livewire.fiscal-approval', [
            'submissions' => $submissions,
        ]);
    }

    public function viewSubmission(int $submissionId)
    {
        $this->selectedSubmission = ReportSubmission::with(['guardUser', 'fiscal'])->findOrFail($submissionId);

        if ($this->selectedSubmission->type === 'private') {
            $this->submissionEntries = PrivateEntry::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                ->where('report_submission_id', $submissionId)
                ->get();
        } else {
            $this->submissionEntries = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                ->where('report_submission_id', $submissionId)
                ->get();
            $this->totalDistance = $this->submissionEntries->sum('distance_traveled');
        }

        $this->showDetailsModal = true;
    }

    public function approveSubmission()
    {
        if (!$this->selectedSubmission) {
            session()->flash('error', 'Nenhum relatório selecionado para aprovação.');
            return;
        }

        $this->selectedSubmission->update([
            'fiscal_id'   => Auth::id(), // Registra quem aprovou
            'approved_at' => now(),
            'status'      => 'approved',
        ]);

        // Atribui o fiscal que aprovou ao relatório, caso não tenha sido atribuído antes
        if (is_null($this->selectedSubmission->assigned_fiscal_id)) {
            $this->selectedSubmission->assigned_fiscal_id = Auth::id();
            $this->selectedSubmission->save();
        }

        session()->flash('message', 'Relatório do porteiro ' . $this->selectedSubmission->guardUser?->name . ' foi aprovado com sucesso.');

        $this->showDetailsModal = false;
        $this->reset('selectedSubmission');
    }

    public function cancelView()
    {
        $this->showDetailsModal = false;
        $this->reset('selectedSubmission', 'totalDistance');
    }
}
