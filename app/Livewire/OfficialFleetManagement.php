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
use Illuminate\Validation\ValidationException; // Importar para erros customizados

#[Layout('layouts.app')]
class OfficialFleetManagement extends Component
{
    use WithPagination;
    use WithSearchableDropdowns;

    // --- PROPRIEDADES EXISTENTES ---
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

    // --- NOVA PROPRIEDADE ---
    public $lastOdometer = null; // Para guardar e exibir o último odómetro registado

    public function layoutData()
    {
        return ['header' => 'Diário de Bordo - Frota Oficial'];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * NOVA FUNÇÃO: Executada sempre que o `vehicle_id` muda no formulário de saída.
     * Busca o último odómetro registado para o veículo selecionado.
     */
    public function updatedVehicleId($vehicleId)
    {
        if ($vehicleId) {
            $lastTrip = OfficialTrip::where('vehicle_id', $vehicleId)
                ->whereNotNull('arrival_odometer')
                ->latest('arrival_datetime')
                ->first();

            // Define o último odómetro encontrado ou 0 se for a primeira viagem
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

    /**
     * MÉTODO DE SAÍDA ATUALIZADO
     */
    public function storeDeparture()
    {
        // Limpeza do valor do odómetro (já existente no seu código)
        if (is_string($this->departure_odometer)) {
            $this->departure_odometer = str_replace(['.', ','], '', $this->departure_odometer);
        }

        // Validação dos campos, incluindo a verificação de que o veículo não está em viagem
        $this->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id', Rule::unique('official_trips')->whereNull('arrival_datetime')],
            'driver_id' => ['required', 'exists:drivers,id', Rule::unique('official_trips')->whereNull('arrival_datetime')], // A regra unique para motorista pode ser muito restritiva, opcional
            'destination' => 'required|string|max:255',
            'departure_odometer' => 'required|integer|min:0', // Garante que é um número
        ], [
            'vehicle_id.required' => 'O campo veículo é obrigatório.',
            'vehicle_id.unique' => 'Este veículo já está em viagem.',
            'driver_id.required' => 'O campo condutor é obrigatório.',
            'driver_id.unique' => 'Este motorista já está em viagem.',
        ]);

        // ### VALIDAÇÃO PRINCIPAL DA SAÍDA ###
        // Compara o odómetro de saída com o último odómetro registado
        $this->updatedVehicleId($this->vehicle_id); // Garante que lastOdometer está atualizado
        if ($this->departure_odometer < $this->lastOdometer) {
            throw ValidationException::withMessages([
                'departure_odometer' => 'O odómetro de saída não pode ser menor que o da última chegada (' . $this->lastOdometer . ' km).',
            ]);
        }

        OfficialTrip::create([
            'vehicle_id' => $this->vehicle_id,
            'driver_id' => $this->driver_id,
            'destination' => $this->destination,
            'departure_odometer' => $this->departure_odometer,
            'departure_datetime' => now(),
            'passengers' => $this->passengers,
            'guard_on_departure' => auth()->user()->name,
            // Removido 'user_id' e 'return_observation' que não pertencem à saída
        ]);

        session()->flash('successMessage', 'Saída de veículo oficial registrada com sucesso!');
        $this->closeDepartureModal();
    }

    public function openArrivalModal($tripId)
    {
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        // Preenche o campo com o odómetro de partida para facilitar, mas o porteiro deve alterar
        $this->arrival_odometer = ''; // Limpa o campo para forçar a inserção
        $this->return_observation = ''; // Limpa a observação
        $this->isArrivalModalOpen = true;
    }

    /**
     * MÉTODO DE CHEGADA ATUALIZADO
     */
    public function storeArrival()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Ação não autorizada.');
        }

        if (is_string($this->arrival_odometer)) {
            $this->arrival_odometer = str_replace(['.', ','], '', $this->arrival_odometer);
        }

        // ### VALIDAÇÃO PRINCIPAL DA CHEGADA ###
        // O odómetro de chegada deve ser um inteiro e maior ou igual ao de partida
        // Usar `gt` (greater than) para garantir que andou pelo menos 1km
        $this->validate([
            'arrival_odometer' => 'required|integer|gt:' . $this->tripToUpdate->departure_odometer,
            'return_observation' => 'nullable|string|max:500',
        ], [
            'arrival_odometer.gt' => 'O odómetro de chegada deve ser maior que o de saída (' . $this->tripToUpdate->departure_odometer . ' km).',
        ]);

        // ### CÁLCULO AUTOMÁTICO DA DISTÂNCIA ###
        $distance = $this->arrival_odometer - $this->tripToUpdate->departure_odometer;

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer,
            'arrival_datetime' => now(),
            'guard_on_arrival' => auth()->user()->name,
            'return_observation' => $this->return_observation,
            'distance_traveled' => $distance, // Guarda a distância calculada
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
        // Limpa especificamente cada propriedade do formulário de saída.
        $this->reset([
            'vehicle_id',
            'driver_id',
            'destination',
            'departure_odometer',
            'passengers',
            'return_observation',
            'vehicle_search',
            'driver_search',
            'lastOdometer'
        ]);

        // Limpa também quaisquer erros de validação antigos.
        $this->resetErrorBag();
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
        $this->reset('arrival_odometer', 'return_observation');
    }

    public function render()
    {
        // O seu método render continua o mesmo...
        $ongoingTrips = OfficialTrip::whereNull('arrival_datetime')
            ->with([
                'vehicle' => fn($query) => $query->withTrashed(),
                'driver' => fn($query) => $query->withTrashed()
            ])
            ->latest('departure_datetime')
            ->get();

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
