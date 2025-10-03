<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use App\Models\ReportSubmission;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GuardReport extends Component
{
    public string $startDate;
    public string $endDate;
    public string $submissionType = 'private';

    // Propriedades para a aba "Oficiais"
    public ?int $selectedVehicleId = null;
    public Collection $vehiclesWithOfficialTrips;
    public Collection $selectedVehicleEntries;

    // ---- PROPRIEDADES PARA O MODAL DE CONFIRMAÇÃO ----
    public bool $showConfirmationModal = false;
    public string $confirmationTitle = '';
    public string $confirmationMessage = '';
    public string $confirmedAction = '';
    public array $confirmedParams = [];
    // ---------------------------------------------------------

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        $this->vehiclesWithOfficialTrips = collect();
        $this->selectedVehicleEntries = collect();
    }

    public function layoutData()
    {
        return ['header' => 'Meus Relatórios Pendentes'];
    }

    public function updated()
    {
        $this->reset('selectedVehicleId');
        $this->loadData();
    }

    public function setSubmissionType(string $type)
    {
        $this->submissionType = $type;
        $this->reset('selectedVehicleId');
        $this->loadData();
    }

    public function loadData()
    {
        $guardName = Auth::user()->name;
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        if ($this->submissionType === 'official') {
            $trips = OfficialTrip::with('vehicle')
                ->where('guard_on_departure', $guardName)
                ->whereBetween('departure_datetime', [$start, $end])
                ->whereNull('report_submission_id')
                ->get();

            $this->vehiclesWithOfficialTrips = $trips->groupBy('vehicle_id')->map(function ($vehicleTrips) {
                return [
                    'vehicle' => $vehicleTrips->first()->vehicle,
                    'count' => $vehicleTrips->count(),
                ];
            });
        }
    }

    public function selectVehicle(int $vehicleId)
    {
        $this->selectedVehicleId = $vehicleId;

        $guardName = Auth::user()->name;
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $this->selectedVehicleEntries = OfficialTrip::with('driver', 'vehicle')
            ->where('guard_on_departure', $guardName)
            ->where('vehicle_id', $this->selectedVehicleId)
            ->whereBetween('departure_datetime', [$start, $end])
            ->whereNull('report_submission_id')
            ->orderBy('departure_datetime', 'asc')
            ->get();
    }

    // ---- NOVOS MÉTODOS PARA O MODAL ----

    /**
     * Prepara e abre o modal de confirmação.
     */
    public function confirmAction(string $action, string $title, string $message, array $params = [])
    {
        $this->confirmedAction = $action;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->confirmedParams = $params;
        $this->showConfirmationModal = true;
    }

    /**
     * Executa a ação que foi confirmada no modal.
     */
    public function executeConfirmedAction()
    {
        // Chama o método guardado em $confirmedAction
        call_user_func_array([$this, $this->confirmedAction], $this->confirmedParams);

        $this->showConfirmationModal = false;
    }
    // ------------------------------------

   

    

    public function render()
    {
        $privateEntries = collect();
        if ($this->submissionType === 'private') {
            $guardName = Auth::user()->name;
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $privateEntries = PrivateEntry::with('vehicle', 'driver')
                ->where('guard_on_entry', $guardName)
                ->whereBetween('entry_at', [$start, $end])
                ->whereNull('report_submission_id')
                ->orderBy('entry_at', 'desc')
                ->paginate(15);
        }

        return view('livewire.guard-report', [
            'privateEntries' => $privateEntries,
        ]);
    }
}
