<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use App\Models\ReportSubmission;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GuardReport extends Component
{
    use WithPagination;

    // Propriedades do Componente
    public string $startDate;
    public string $endDate;
    public string $submissionType = 'private';
    public string $observation = '';
    public ?int $selectedVehicleId = null;
    public Collection $selectedVehicleEntries;

    // Propriedades do Modal de Confirmação
    public bool $showConfirmationModal = false;
    public string $confirmationTitle = '';
    public string $confirmationMessage = '';
    public string $confirmedAction = '';
    public array $confirmedParams = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        $this->selectedVehicleEntries = collect();
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'submissionType'])) {
            $this->resetPage();
            $this->reset('selectedVehicleId', 'selectedVehicleEntries');
        }
    }

    public function setSubmissionType(string $type)
    {
        $this->submissionType = $type;
        $this->resetPage();
        $this->reset('selectedVehicleId', 'selectedVehicleEntries');
    }

    public function selectVehicle(int $vehicleId)
    {
        $this->selectedVehicleId = $vehicleId;
        $this->selectedVehicleEntries = OfficialTrip::with(['driver', 'vehicle' => fn($q) => $q->withTrashed()])
            ->where('guard_on_departure', Auth::user()->name)
            ->where('vehicle_id', $this->selectedVehicleId)
            ->whereBetween('departure_datetime', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->whereNull('report_submission_id')
            ->orderBy('departure_datetime', 'asc')
            ->get();
    }

    public function clearSelectedVehicle()
    {
        $this->reset('selectedVehicleId', 'selectedVehicleEntries', 'observation');
    }

    // --- NOVA LÓGICA DE SUBMISSÃO (EFICIENTE) ---

    public function submitPrivateReport()
    {
        $entryIds = PrivateEntry::where('guard_on_entry', Auth::user()->name)
            ->whereBetween('entry_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->whereNull('report_submission_id')
            ->pluck('id');

        if ($entryIds->isEmpty()) {
            session()->flash('error', 'Nenhum registro de veículo particular para submeter no período.');
            return;
        }

        $submission = ReportSubmission::create([
            'user_id'    => Auth::id(),
            'start_date' => $this->startDate,
            'end_date'   => $this->endDate,
            'type'       => 'private',
            'status'     => 'pending',
        ]);

        PrivateEntry::whereIn('id', $entryIds)->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório de ' . $entryIds->count() . ' registros particulares submetido com sucesso!');
        $this->resetPage('privatePage');
    }

    public function submitOfficialReport()
    {
        $this->validate([
            'selectedVehicleId' => 'required',
            'observation' => 'nullable|string|max:500',
            'selectedVehicleEntries' => 'required|min:1'
        ]);

        $submission = ReportSubmission::create([
            'user_id'     => Auth::id(),
            'vehicle_id'  => $this->selectedVehicleId,
            'start_date'  => $this->startDate,
            'end_date'    => $this->endDate,
            'observation' => $this->observation,
            'type'        => 'official',
            'status'      => 'pending',
        ]);

        OfficialTrip::whereIn('id', $this->selectedVehicleEntries->pluck('id'))->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório do veículo submetido com sucesso!');
        $this->clearSelectedVehicle();
    }

    // --- LÓGICA DO MODAL ADAPTADA ---

    public function confirmSubmission(string $type)
    {
        if ($type === 'private') {
            // Conta os registros antes de abrir o modal
            $count = PrivateEntry::where('guard_on_entry', Auth::user()->name)
                ->whereBetween('entry_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
                ->whereNull('report_submission_id')
                ->count();

            if ($count === 0) {
                session()->flash('error', 'Nenhum registro para submeter.');
                return;
            }
            $this->confirmAction(
                'submitPrivateReport',
                'Confirmar Submissão',
                "Tem certeza que deseja submeter os {$count} registros de veículos particulares deste período?"
            );
        } elseif ($type === 'official') {
            $this->validate(['selectedVehicleId' => 'required']);
            $this->confirmAction(
                'submitOfficialReport',
                'Confirmar Submissão',
                'Tem certeza que deseja submeter o relatório para o veículo selecionado? Esta ação não pode ser desfeita.'
            );
        }
    }

    public function confirmAction(string $action, string $title, string $message, array $params = [])
    {
        $this->confirmedAction = $action;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->confirmedParams = $params;
        $this->showConfirmationModal = true;
    }

    public function executeConfirmedAction()
    {
        if (method_exists($this, $this->confirmedAction)) {
            call_user_func_array([$this, $this->confirmedAction], $this->confirmedParams);
        }
        $this->showConfirmationModal = false;
    }

    public function render()
    {
        $privateEntries = collect();
        $vehiclesWithOfficialTrips = collect();
        $officialTrips = null;

        if ($this->submissionType === 'private') {
            $privateEntries = PrivateEntry::with('vehicle', 'driver')
                ->where('guard_on_entry', Auth::user()->name)
                ->whereBetween('entry_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
                ->whereNull('report_submission_id')
                ->orderBy('entry_at', 'desc')
                ->paginate(15, ['*'], 'privatePage');
        }

        if ($this->submissionType === 'official') {
            $officialTrips = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed()])
                ->where('guard_on_departure', Auth::user()->name)
                ->whereBetween('departure_datetime', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
                ->whereNull('report_submission_id')
                ->whereHas('vehicle', fn($q) => $q->withTrashed())
                ->paginate(15, ['*'], 'officialPage');

            $vehiclesWithOfficialTrips = $officialTrips->groupBy('vehicle_id')->map(function ($vehicleTrips) {
                return [
                    'vehicle' => $vehicleTrips->first()->vehicle,
                    'count' => $vehicleTrips->count(),
                ];
            });
        }

        return view('livewire.guard-report', [
            'privateEntries' => $privateEntries,
            'vehiclesWithOfficialTrips' => $vehiclesWithOfficialTrips,
            'officialTrips' => $officialTrips,
        ]);
    }
}
