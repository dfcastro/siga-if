<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // Adicionar se estiver a usar paginação nos parciais

#[Layout('layouts.app')]
class GuardReport extends Component
{
    use WithPagination; // Adicionar se estiver a usar paginação nos parciais

    public string $startDate;
    public string $endDate;
    public string $submissionType = 'private';

    // Propriedades para a aba "Oficiais"
    public ?int $selectedVehicleId = null;
    public Collection $vehiclesWithOfficialTrips;
    public Collection $selectedVehicleEntries;

    // SUAS PROPRIEDADES PARA O MODAL (ESTÃO PERFEITAS)
    public bool $showConfirmationModal = false;
    public string $confirmationTitle = '';
    public string $confirmationMessage = '';
    public string $confirmedAction = '';
    public array $confirmedParams = [];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        $this->loadData(); // Carregar os dados iniciais
    }

    public function updated($property)
    {
        // Apenas recarrega se uma data mudar
        if ($property === 'startDate' || $property === 'endDate') {
            $this->reset('selectedVehicleId');
            $this->loadData();
        }
    }

    public function setSubmissionType(string $type)
    {
        $this->submissionType = $type;
        $this->reset('selectedVehicleId');
        $this->resetPage();
        $this->loadData();
    }

    public function loadData()
    {
        // A sua lógica de carregamento de dados está correta.
        // Assegure-se que ela é chamada quando necessário.
        if ($this->submissionType === 'official') {
            $guardName = Auth::user()->name;
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

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
        // Sua lógica para selecionar veículo está correta
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

    public function clearSelectedVehicle()
    {
        $this->selectedVehicleId = null;
    }

    // --- MÉTODOS PARA O MODAL (USANDO A SUA ESTRUTURA) ---

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
        if ($this->confirmedAction) {
            call_user_func_array([$this, $this->confirmedAction], $this->confirmedParams);
        }
        $this->showConfirmationModal = false;
    }

    /**
     * NOVO: Prepara a confirmação para submeter um formulário.
     */
    public function confirmSubmission(string $formId, string $message)
    {
        $this->confirmAction(
            'submitForm',
            'Confirmar Submissão',
            $message,
            ['formId' => $formId]
        );
    }

    /**
     * NOVO: Despacha o evento que o JavaScript vai ouvir para submeter o form.
     */
    public function submitForm(string $formId)
    {
        $this->dispatch('submit-form', formId: $formId);
    }

    // ---------------------------------------------------------

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
