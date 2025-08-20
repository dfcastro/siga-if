<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Garante que o layout principal seja usado
class OfficialFleetManagement extends Component
{
    // Propriedades para o modal de SAÍDA
    public $vehicle_id = '';
    public $driver_id = '';
    public string $destination = '';
    public $departure_odometer = '';
    public ?string $passengers = '';
    public bool $isDepartureModalOpen = false;

    // Propriedades para o modal de CHEGADA
    public ?OfficialTrip $tripToUpdate = null;
    public $arrival_odometer = '';
    public bool $isArrivalModalOpen = false;

    // Propriedade para a mensagem de sucesso
    public string $successMessage = '';

    // Envia o título da página para o layout
    public function layoutData()
    {
        return ['header' => 'Diário de Bordo - Frota Oficial'];
    }

    public function render()
    {
        $ongoingTrips = OfficialTrip::with(['vehicle', 'driver'])->whereNull('arrival_datetime')->latest('departure_datetime')->get();
        $completedTrips = OfficialTrip::with(['vehicle', 'driver'])->whereNotNull('arrival_datetime')->latest('arrival_datetime')->take(5)->get();
        $officialVehicles = Vehicle::where('type', 'Oficial')->orderBy('license_plate')->get();
        
        // MELHORIA: Busca apenas motoristas autorizados
        $drivers = Driver::where('is_authorized', true)->orderBy('name')->get();

        return view('livewire.official-fleet-management', [
            'ongoingTrips' => $ongoingTrips,
            'completedTrips' => $completedTrips,
            'officialVehicles' => $officialVehicles,
            'drivers' => $drivers,
        ]);
    }

    // --- MÉTODOS PARA REGISTRO DE SAÍDA ---
    public function storeDeparture()
    {
        $validatedData = $this->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('type', 'Oficial'), Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'driver_id' => ['required', Rule::exists('drivers', 'id')->where('is_authorized', true)],
            'destination' => 'required|min:3',
            'departure_odometer' => 'required|integer|min:0',
            'passengers' => 'nullable|string',
        ], ['vehicle_id.unique' => 'Este veículo já está em uma viagem em andamento.', 'driver_id.exists' => 'O condutor selecionado não está autorizado.']);

        OfficialTrip::create(array_merge($validatedData, [
            'departure_datetime' => now(),
            'guard_on_departure' => auth()->user()->name, // MELHORIA: Usa o nome do usuário logado
        ]));
        
        $this->successMessage = 'Saída de veículo registrada com sucesso!';
        $this->closeDepartureModal();
    }

    public function create() { $this->resetDepartureFields(); $this->isDepartureModalOpen = true; }
    public function closeDepartureModal() { $this->isDepartureModalOpen = false; }
    private function resetDepartureFields() { $this->reset(['vehicle_id', 'driver_id', 'destination', 'departure_odometer', 'passengers']); $this->resetErrorBag(); }

    // --- NOVOS MÉTODOS PARA REGISTRO DE CHEGADA ---
    public function storeArrival()
    {
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