<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Livewire\Traits\WithSearchableDropdowns;

#[Layout('layouts.app')]
class OfficialFleetManagement extends Component
{
    use WithPagination;
    use WithSearchableDropdowns; 

    // ... (suas propriedades não mudam)
    public $vehicle_id = null;
    public $driver_id = null;
    public string $destination = '';
    public $departure_odometer = '';
    public ?string $passengers = '';
    public $return_observation = '';
    public string $vehicle_search = '';
    public string $driver_search = '';
    public $vehicle_results = [];
    public $driver_results = [];
    public bool $show_vehicle_dropdown = false;
    public bool $show_driver_dropdown = false;
    public bool $isDepartureModalOpen = false;
    public ?OfficialTrip $tripToUpdate = null;
    public $arrival_odometer = '';
    public bool $isArrivalModalOpen = false;
    public string $search = '';


    public function layoutData()
    {
        return ['header' => 'Diário de Bordo - Frota Oficial'];
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // public function updatedVehicleSearch(string $value): void
    // {
    //     if (strlen($value) >= 2) {
    //         $ongoingTripVehicleIds = OfficialTrip::whereNull('arrival_datetime')->pluck('vehicle_id');
    //         // --- CORREÇÃO: Removido o .toArray() para retornar uma Coleção de Objetos ---
    //         $this->vehicle_results = Vehicle::where('type', 'Oficial')
    //             ->whereNotIn('id', $ongoingTripVehicleIds)
    //             ->where(fn($q) => $q->where('model', 'like', "%{$value}%")->orWhere('license_plate', 'like', "%{$value}%"))
    //             ->limit(5)->get();
    //         $this->show_vehicle_dropdown = true;
    //     } else {
    //         $this->vehicle_results = collect(); // Usar uma coleção vazia
    //         $this->show_vehicle_dropdown = false;
    //     }
    //     $this->vehicle_id = null;
    // }

    // public function updatedDriverSearch(string $value): void
    // {
    //     if (strlen($value) >= 2) {

    //         $this->driver_results = Driver::where('name', 'like', '%' . $value . '%')
    //             ->where('is_authorized', true)
    //             ->limit(5)->get();
    //         $this->show_driver_dropdown = true;
    //     } else {
    //         $this->driver_results = collect();
    //         $this->show_driver_dropdown = false;
    //     }
    //     $this->driver_id = null;
    // }

    // public function selectVehicle($id, $text)
    // {
    //     $this->vehicle_id = $id;
    //     $this->vehicle_search = $text;
    //     $this->show_vehicle_dropdown = false;
    //     $this->vehicle_results = collect();
    // }
    
    // public function selectDriver($id, $name)
    // {
    //     $this->driver_id = $id;
    //     $this->driver_search = $name;
    //     $this->show_driver_dropdown = false;
    //     $this->driver_results = collect();
    // }

    public function create()
    {
        $this->resetDepartureForm();
        $this->isDepartureModalOpen = true;
    }

    public function storeDeparture()
    {
        if (is_string($this->departure_odometer)) {
            $this->departure_odometer = str_replace('.', '', $this->departure_odometer);
        }

        $this->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id', Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'driver_id' => ['required', 'exists:drivers,id', Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'destination' => 'required|string|max:255',
            'departure_odometer' => 'required|integer',
        ], [
            'vehicle_id.required' => 'O campo veículo é obrigatório.',
            'vehicle_id.unique' => 'Este veículo já está em viagem.',
            'driver_id.required' => 'O campo condutor é obrigatório.',
            'driver_id.unique' => 'Este motorista já está em viagem.',
        ]);

        OfficialTrip::create([
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'destination' => $this->destination,
            'departure_odometer' => $this->departure_odometer,
            'departure_datetime' => now(),
            'passengers' => $this->passengers,
            'return_observation' => $this->return_observation,
            'user_id' => auth()->id(),
            'guard_on_departure' => auth()->user()->name,
        ]);

        session()->flash('successMessage', 'Saída de veículo oficial registrada com sucesso!');
        $this->closeDepartureModal();
    }

    public function openArrivalModal($tripId)
    {
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        $this->arrival_odometer = $this->tripToUpdate->departure_odometer;
        $this->isArrivalModalOpen = true;
    }

    public function storeArrival()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Ação não autorizada.');
        }

        if (is_string($this->arrival_odometer)) {
            $this->arrival_odometer = str_replace('.', '', $this->arrival_odometer);
        }

        $this->validate([
            'arrival_odometer' => 'required|integer|min:' . $this->tripToUpdate->departure_odometer
        ], [
            'arrival_odometer.min' => 'A quilometragem de chegada não pode ser menor que a de saída.'
        ]);

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer,
            'arrival_datetime' => now(),
            'guard_on_arrival' => auth()->user()->name,
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
        $this->reset();
        $this->resetErrorBag();
    }
    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
    }

   public function render()
    {
        // **CORREÇÃO APLICADA AQUI**
        // Agora, ao buscar os relacionamentos 'vehicle' e 'driver', incluímos os que estão na lixeira.
        $ongoingTrips = OfficialTrip::whereNull('arrival_datetime')
            ->with([
                'vehicle' => fn($query) => $query->withTrashed(),
                'driver' => fn($query) => $query->withTrashed()
            ])
            ->latest('departure_datetime')
            ->get();

        // **CORREÇÃO APLICADA AQUI TAMBÉM**
        $completedTripsQuery = OfficialTrip::whereNotNull('arrival_datetime')
            ->with([
                'vehicle' => fn($query) => $query->withTrashed(),
                'driver' => fn($query) => $query->withTrashed()
            ]);

        if (!empty($this->search)) {
            $completedTripsQuery->where(function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('destination', 'like', $searchTerm)
                    ->orWhereHas('vehicle', fn($q) => $q->withTrashed()->where('model', 'like', $searchTerm)->orWhere('license_plate', 'like', $searchTerm))
                    ->orWhereHas('driver', fn($q) => $q->withTrashed()->where('name', 'like', $searchTerm));
            });
        }

        $completedTrips = $completedTripsQuery->latest('arrival_datetime')->paginate(10);

        return view('livewire.official-fleet-management', [
            'ongoingTrips' => $ongoingTrips, 
            'completedTrips' => $completedTrips
        ]);
    }
}

