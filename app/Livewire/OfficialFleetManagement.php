<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class OfficialFleetManagement extends Component
{
    use WithPagination;

    // --- PROPRIEDADES DO FORMULÁRIO DE SAÍDA ---
    public $vehicle_id = '';
    public $driver_id = '';
    public string $destination = '';
    public $departure_odometer = '';
    public ?string $passengers = '';
    public $return_observation = '';

    // --- CONTROLES DE MODAL E CHEGADA ---
    public bool $isDepartureModalOpen = false;
    public ?OfficialTrip $tripToUpdate = null;
    public $arrival_odometer = '';
    public bool $isArrivalModalOpen = false;
    public string $search = '';

    // --- OUTROS ---
    public $lastOdometer = null;

    public function layoutData()
    {
        return ['header' => 'Diário de Bordo - Frota Oficial'];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedVehicleId($vehicleId)
    {
        if ($vehicleId) {
            $lastTrip = OfficialTrip::where('vehicle_id', $vehicleId)
                ->whereNotNull('arrival_odometer')
                ->latest('arrival_datetime')
                ->first();

            $this->lastOdometer = $lastTrip ? $lastTrip->arrival_odometer : 0;
        } else {
            $this->lastOdometer = null;
        }
    }

    public function create()
    {
        $this->resetDepartureForm();
        $this->isDepartureModalOpen = true;
    }

    public function storeDeparture()
    {
        if (is_string($this->departure_odometer)) {
            $this->departure_odometer = str_replace(['.', ','], '', $this->departure_odometer);
        }

        $this->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id', Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'driver_id' => ['required', 'exists:drivers,id', Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'destination' => 'required|string|max:255',
            'departure_odometer' => 'required|integer|min:0',
            'return_observation' => 'nullable|string|max:1000',
        ], [
            'vehicle_id.required' => 'Selecione a viatura que irá sair.',
            'vehicle_id.unique' => 'Este veículo já consta como em viagem no sistema.',
            'driver_id.required' => 'Selecione o condutor.',
            'driver_id.unique' => 'Este motorista já consta como em viagem no sistema.',
        ]);

        $this->updatedVehicleId($this->vehicle_id);
        if ($this->departure_odometer < $this->lastOdometer) {
            throw ValidationException::withMessages([
                'departure_odometer' => 'O odómetro não pode ser menor que o da última chegada registrada (' . $this->lastOdometer . ' km).',
            ]);
        }

        OfficialTrip::create([
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'destination' => $this->destination,
            'departure_odometer' => $this->departure_odometer,
            'departure_datetime' => now(),
            'passengers' => $this->passengers,
            'return_observation' => $this->return_observation,
            'guard_on_departure_id' => Auth::id(),
        ]);

        session()->flash('successMessage', 'Saída de veículo oficial registrada com sucesso!');
        $this->closeDepartureModal();
    }

    public function openArrivalModal($tripId)
    {
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        $this->arrival_odometer = '';
        $this->return_observation = $this->tripToUpdate->return_observation;
        $this->isArrivalModalOpen = true;
    }

    public function storeArrival()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Ação não autorizada.');
        }

        if (is_string($this->arrival_odometer)) {
            $this->arrival_odometer = str_replace(['.', ','], '', $this->arrival_odometer);
        }

        $this->validate([
            'arrival_odometer' => 'required|integer|gt:' . $this->tripToUpdate->departure_odometer,
            'return_observation' => 'nullable|string|max:1000',
        ], [
            'arrival_odometer.gt' => 'O odómetro de chegada deve ser maior que o de saída (' . $this->tripToUpdate->departure_odometer . ' km).',
        ]);

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer,
            'arrival_datetime' => now(),
            'guard_on_arrival_id' => Auth::id(),
            'return_observation' => $this->return_observation,
        ]);

        session()->flash('successMessage', 'Chegada de veículo registrada com sucesso!');
        $this->closeArrivalModal();
    }

    public function closeDepartureModal()
    {
        $this->isDepartureModalOpen = false;
        $this->resetDepartureForm();
    }

    public function resetDepartureForm()
    {
        $this->reset([
            'vehicle_id',
            'driver_id',
            'destination',
            'departure_odometer',
            'passengers',
            'return_observation',
            'lastOdometer'
        ]);
        $this->resetErrorBag();
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
        $this->reset('arrival_odometer', 'return_observation');
        $this->resetErrorBag(['arrival_odometer', 'return_observation']);
    }

    public function render()
    {
        // Query de Listagem Normal
        $ongoingTrips = OfficialTrip::whereNull('arrival_datetime')
            ->with(['vehicle' => fn($query) => $query->withTrashed(), 'driver' => fn($query) => $query->withTrashed()])
            ->latest('departure_datetime')
            ->get();

        $completedTripsQuery = OfficialTrip::whereNotNull('arrival_datetime')
            ->with(['vehicle' => fn($query) => $query->withTrashed(), 'driver' => fn($query) => $query->withTrashed()]);

        if (!empty($this->search)) {
            $completedTripsQuery->where(function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('destination', 'like', $searchTerm)
                    ->orWhereHas('vehicle', fn($q) => $q->withTrashed()->where('model', 'like', $searchTerm)->orWhere('license_plate', 'like', $searchTerm))
                    ->orWhereHas('driver', fn($q) => $q->withTrashed()->where('name', 'like', $searchTerm));
            });
        }

        $completedTrips = $completedTripsQuery->latest('arrival_datetime')->paginate(10);

        // --- DADOS PARA O DROPDOWN (Novidade) ---
        // Pega todos os veículos oficiais e os condutores autorizados
        $officialVehicles = Vehicle::where('type', 'Oficial')->orderBy('model')->get();
        $authorizedDrivers = Driver::where('is_authorized', true)->orderBy('name')->get();

        return view('livewire.official-fleet-management', [
            'ongoingTrips' => $ongoingTrips,
            'completedTrips' => $completedTrips,
            'officialVehicles' => $officialVehicles,
            'authorizedDrivers' => $authorizedDrivers,
        ]);
    }
}
