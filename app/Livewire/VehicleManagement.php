<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Illuminate\Pagination\LengthAwarePaginator; // Importar o paginador

#[Layout('layouts.app')]
class VehicleManagement extends Component
{
    use WithPagination;

    // Propriedades do formulário
    public string $license_plate = '';
    public string $model = '';
    public string $color = '';
    public $driver_id = '';
    public $vehicleId;
    public string $type = 'Particular';

    // Controles da UI
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $vehicleIdToDelete;
    public $vehiclePlateToDelete;
    public string $successMessage = '';
    public string $search = '';
    public string $filter = 'active';

    // Propriedades para o modal de histórico
    public $isHistoryModalOpen = false;
    public $vehicleForHistory = null;
    // A propriedade $vehicleHistory foi removida daqui para ser calculada no render()

    public array $commonColors = ['PRETO', 'BRANCO', 'PRATA', 'CINZA', 'VERMELHO', 'AZUL', 'VERDE', 'AMARELO', 'DOURADO', 'MARROM', 'BEGE', 'LARANJA', 'ROXO'];

    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // --- Lógica da lista principal de veículos (sem alterações) ---
        $query = Vehicle::with('driver');
        if ($this->filter === 'trashed') {
            $query->onlyTrashed();
        }
        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('license_plate', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('color', 'like', $searchTerm)
                    ->orWhereHas('driver', function ($driverQuery) use ($searchTerm) {
                        $driverQuery->where('name', 'like', $searchTerm);
                    });
            });
        }
        $vehicles = $query->orderBy('model', 'asc')->paginate(10);
        $drivers = Driver::orderBy('name')->get();

        // --- ATUALIZADO: Lógica do histórico movida para aqui ---
        $vehicleHistoryPaginator = null; // Inicializa como nulo
        if ($this->isHistoryModalOpen && $this->vehicleForHistory) {

            // Recarrega as relações para garantir que os dados estão atualizados
            $this->vehicleForHistory->load('privateEntries.driver', 'officialTrips.driver');

            // Mapeia entradas particulares
            $privateEntries = $this->vehicleForHistory->privateEntries->map(function ($entry) {
                return [
                    'type' => 'Particular',
                    'start_time' => $entry->entry_at,
                    'end_time' => $entry->exit_at,
                    'driver_name' => $entry->driver->name ?? 'Não informado',
                    'detail' => $entry->entry_reason,
                    'guard_entry' => $entry->guard_on_entry,
                    'guard_exit' => $entry->guard_on_exit,
                ];
            });

            // Mapeia viagens oficiais
            $officialTrips = $this->vehicleForHistory->officialTrips->map(function ($trip) {
                return [
                    'type' => 'Oficial',
                    'start_time' => $trip->departure_datetime,
                    'end_time' => $trip->arrival_datetime,
                    'driver_name' => $trip->driver->name ?? 'Não informado',
                    'detail' => $trip->destination,
                    'guard_entry' => $trip->guard_on_departure,
                    'guard_exit' => $trip->guard_on_arrival,
                ];
            });

            // Combina, ordena e cria a coleção paginada
            $fullHistory = $privateEntries->concat($officialTrips)->sortByDesc('start_time')->values();
            $currentPage = LengthAwarePaginator::resolveCurrentPage('historyPage');
            $perPage = 5;
            $currentPageItems = $fullHistory->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $vehicleHistoryPaginator = new LengthAwarePaginator($currentPageItems, $fullHistory->count(), $perPage, $currentPage, [
                'path' => request()->url(),
                'pageName' => 'historyPage',
            ]);
        }

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'vehicleHistory' => $vehicleHistoryPaginator, // Passa o paginador para a view
        ]);
    }



    // Salva um novo registro ou atualiza um existente
    public function store()
    {
        $this->validate([
            'license_plate' => [
                'required',
                'string',
                'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}-[0-9]{4}$/i',
                Rule::unique('vehicles')->ignore($this->vehicleId)
            ],
            // DICA APLICADA: Limite de 20 caracteres
            'model' => 'required|string|max:25',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:Oficial,Particular',
            'driver_id' => 'nullable|exists:drivers,id',
        ], [
            'license_plate.regex' => 'O formato da placa é inválido. Use AAA-1234 ou ABC1D23.'
        ]);

        // CORREÇÃO DO ERRO SQL: Converte string vazia para null
        $driverId = $this->driver_id === '' ? null : $this->driver_id;

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => strtoupper($this->license_plate),
            'model'         => Str::upper($this->model),
            'color'         => Str::upper($this->color),
            'type' => $this->type,
            'driver_id' => $driverId,
        ]);

        session()->flash('successMessage', $this->vehicleId ? 'Veículo atualizado com sucesso!' : 'Veículo criado com sucesso!');

        $this->closeModal();
    }

    // Abre o modal para criar um novo registro
    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
        $this->dispatch('init-tom-select');
    }

    // Abre o modal para editar um registro existente
    public function edit($id)
    {
        // Busca o veículo mesmo que esteja na lixeira
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $this->vehicleId = $id;
        $this->license_plate = $vehicle->license_plate;
        $this->model = $vehicle->model;
        $this->color = $vehicle->color;
        $this->driver_id = $vehicle->driver_id;
        $this->type = $vehicle->type;
        $this->isModalOpen = true;
        $this->dispatch('init-tom-select', ['driverId' => $this->driver_id]);
    }

    public function closeModal()
    {

        $this->isModalOpen = false;
        $this->resetInputFields();
        $this->dispatch('destroy-tom-select');
    }

    private function resetInputFields()
    {
        $this->reset(['license_plate', 'model', 'color', 'driver_id', 'vehicleId', 'type']); // ALTERADO: Reseta o tipo
        $this->resetErrorBag();
        $this->dispatch('reset-tom-select');
    }


    // Abre o modal de confirmação de exclusão
    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    // Exclui o veículo
    public function deleteVehicle()
    {
        Vehicle::find($this->vehicleIdToDelete)->delete(); // Isto agora move para a lixeira
        session()->flash('successMessage', 'Veículo movido para a lixeira com sucesso!');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    // ADICIONADO: Método para restaurar
    public function restore($id)
    {
        Vehicle::withTrashed()->find($id)->restore();
        session()->flash('successMessage', 'Veículo restaurado com sucesso!');
    }

    // ADICIONADO: Método para confirmar a exclusão permanente
    public function confirmForceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    // Método para apagar permanentemente
    public function forceDeleteVehicle()
    {
        Vehicle::withTrashed()->find($this->vehicleIdToDelete)->forceDelete();
        session()->flash('successMessage', 'Veículo excluído permanentemente!');
        $this->closeConfirmModal();
    }

    /**
     *  método para buscar e exibir o histórico do veículo.
     */
    public function showHistory($vehicleId)
    {
        $this->vehicleForHistory = Vehicle::withTrashed()->findOrFail($vehicleId);
        $this->resetPage('historyPage'); // Reseta para a página 1 sempre que abrir o modal
        $this->isHistoryModalOpen = true;
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->vehicleForHistory = null;
    }
}
