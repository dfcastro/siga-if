<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')] // Garante que o layout principal seja usado
class OfficialFleetManagement extends Component
{

    use WithPagination; //USAR A TRAIT DE PAGINAÇÃO
    // Propriedades para o modal de SAÍDA
    public $vehicle_id = '';
    public $driver_id = '';
    public string $destination = '';
    public $departure_odometer = '';
    public ?string $passengers = '';
    public bool $isDepartureModalOpen = false;
    public $return_observation = '';

    // Propriedades para o modal de CHEGADA
    public ?OfficialTrip $tripToUpdate = null;
    public $arrival_odometer = '';
    public bool $isArrivalModalOpen = false;

    // Propriedade para a mensagem de sucesso
    public string $successMessage = '';

    //PROPRIEDADE PÚBLICA PARA A BUSCA
    public string $search = '';



    //MÉTODO PARA RESETAR A PAGINAÇÃO AO BUSCAR
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Envia o título da página para o layout
    public function layoutData()
    {
        return ['header' => 'Diário de Bordo - Frota Oficial'];
    }

    public function render()
    {
        $ongoingTrips = OfficialTrip::whereNull('arrival_datetime')
            ->with(['vehicle', 'driver'])
            ->latest('departure_datetime')
            ->get();

        // 3. ATUALIZAR A CONSULTA PARA INCLUIR A LÓGICA DE BUSCA
        $completedTripsQuery = OfficialTrip::whereNotNull('arrival_datetime')
            ->with(['vehicle', 'driver']);

        if (!empty($this->search)) {
            $completedTripsQuery->where(function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where('destination', 'like', $searchTerm)
                    ->orWhereHas('vehicle', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('model', 'like', $searchTerm)
                            ->orWhere('license_plate', 'like', $searchTerm);
                    })
                    ->orWhereHas('driver', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        $completedTrips = $completedTripsQuery->latest('arrival_datetime')->paginate(10);

        $officialVehicles = Vehicle::where('type', 'oficial')->get();
        $drivers = Driver::where('is_authorized', true)->get();

        return view('livewire.official-fleet-management', [
            'ongoingTrips'     => $ongoingTrips,
            'completedTrips'   => $completedTrips,
            'officialVehicles' => $officialVehicles,
            'drivers'          => $drivers,
        ]);
    }

    // --- MÉTODOS PARA REGISTRO DE SAÍDA ---
    public function storeDeparture()
    {


        $validatedData = $this->validate([
            'vehicle_id' =>  'required',
                'exists:vehicles,id',
                Rule::unique('official_trips')->where(function ($query) {
                    return $query->whereNull('arrival_datetime');
                })
            ],
            [
            'driver_id' => 'required|exists:drivers,id',
            'destination' => 'required|string|max:255',
            'departure_odometer' => 'required|integer',
            'passengers' => 'nullable|string|max:1000',
            'return_observation' => 'nullable|string|max:1000',
            ],[
            'vehicle_id.unique' => 'Este veículo já está em viagem. Registre a chegada antes de uma nova saída.'
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
        $this->dispatch('trip-created');
    }

    public function resetDepartureForm()
    {
        $this->reset([
            'vehicle_id',
            'driver_id',
            'destination',
            'departure_odometer',
            'passengers',
            'return_observation', // Adicionado para limpar o novo campo também
        ]);
        $this->resetErrorBag();
    }

    public function create()
    {
        // Obtém os IDs de todos os veículos que já estão em viagem
        $ongoingTripVehicleIds = OfficialTrip::whereNull('arrival_datetime')->pluck('vehicle_id')->toArray();

        // Busca todos os veículos oficiais que NÃO ESTÃO na lista de veículos em viagem
        $this->officialVehicles = Vehicle::where('type', 'Oficial')
            ->whereNotIn('id', $ongoingTripVehicleIds) 
            ->orderBy('model')
            ->get();

        $this->drivers = Driver::orderBy('name')->get();
        $this->resetDepartureForm();
        $this->isDepartureModalOpen = true;
        $this->dispatch('init-fleet-selectors');
    }



    public function closeDepartureModal()
    {
        $this->isDepartureModalOpen = false;
    }

    private function resetDepartureFields()
    {
        $this->reset(['vehicle_id', 'driver_id', 'destination', 'departure_odometer', 'passengers', 'successMessage']);
        $this->resetErrorBag();
        // AVISA O BROWSER PARA LIMPAR OS SELETORES
        $this->dispatch('init-fleet-selectors');
    }
    // --- NOVOS MÉTODOS PARA REGISTRO DE CHEGADA ---
    public function storeArrival()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }
        $trip = OfficialTrip::findOrFail($this->tripToUpdate->id);
        $this->validate([
            // A quilometragem de chegada deve ser um número e no mínimo igual à de saída
            'arrival_odometer' => 'required|integer|min:' . $this->tripToUpdate->departure_odometer
        ], [
            'arrival_odometer.min' => 'A quilometragem de chegada não pode ser menor que a de saída.'
        ]);

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer,
            'arrival_datetime' => now(),
            'guard_on_arrival' => 'Porteiro IFNMG',
        ]);

        session()->flash('success', 'Chegada de veículo registrada com sucesso!');
        $this->closeArrivalModal();
    }

    public function openArrivalModal($tripId)
    {
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        $this->arrival_odometer = $this->tripToUpdate->departure_odometer; // Sugere o KM de partida
        $this->isArrivalModalOpen = true;
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
    }
}
