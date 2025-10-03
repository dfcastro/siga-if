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

    public function layoutData()
    {
        return ['header' => 'Aprovação e Arquivo de Relatórios'];
    }

    public function setFilter(string $status)
    {
        $this->filterStatus = $status;
        $this->resetPage(); // Reseta a paginação ao mudar de aba
    }

    public function render()
    {
        // Usamos o null-safe operator (?->) para segurança, embora guardUser não deva ser nulo
        $query = ReportSubmission::with(['guardUser', 'fiscal'])
            ->where('status', $this->filterStatus);

        if ($this->filterStatus === 'approved') {
            $query->orderBy('approved_at', 'desc');
        } else {
            $query->orderBy('submitted_at', 'asc');
        }

        $submissions = $query->paginate(10);

        return view('livewire.fiscal-approval', [
            'submissions' => $submissions,
        ]);
    }

    public function viewSubmission(int $submissionId)
    {
        $this->selectedSubmission = ReportSubmission::with(['guardUser', 'fiscal'])->findOrFail($submissionId);

        // Busca registos de veículos particulares
        $privateEntries = PrivateEntry::with('vehicle', 'driver')
            ->where('report_submission_id', $submissionId)
            ->get();

        // Busca registos de frota oficial
        // ---- A CORREÇÃO CRÍTICA ESTAVA AQUI ----
        $officialTrips = OfficialTrip::with('vehicle', 'driver')
            ->where('report_submission_id', $submissionId) // Corrigido de '>' para '='
            ->get();

        // Junta as duas coleções e ordena
        $this->submissionEntries = $privateEntries->concat($officialTrips)->sortBy(function ($entry) {
            return $entry instanceof PrivateEntry ? $entry->entry_at : $entry->departure_datetime;
        });

        $this->showDetailsModal = true;
    }

    public function approveSubmission()
    {
        if (!$this->selectedSubmission) {
            session()->flash('error', 'Nenhum relatório selecionado para aprovação.');
            return;
        }

        $this->selectedSubmission->update([
            'fiscal_id'   => Auth::id(),
            'approved_at' => now(),
            'status'      => 'approved',
        ]);

        // Usamos o null-safe operator para segurança
        session()->flash('message', 'Relatório do porteiro ' . $this->selectedSubmission->guardUser?->name . ' foi aprovado com sucesso.');

        $this->showDetailsModal = false;
        $this->reset('selectedSubmission');
    }

    public function cancelView()
    {
        $this->showDetailsModal = false;
        $this->reset('selectedSubmission');
    }
}
