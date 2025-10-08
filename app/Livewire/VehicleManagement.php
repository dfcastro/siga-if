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
use Illuminate\Pagination\LengthAwarePaginator;

#[Layout('layouts.app')]
class VehicleManagement extends Component
{
    use WithPagination;

    // --- PROPRIEDADES DO FORMULÁRIO ---
    public string $license_plate = '';
    public string $model = '';
    public string $color = '';
    public $vehicleId;
    public string $type = 'Particular';
    public $driver_id = '';
    public string $driver_search = '';
    public bool $show_driver_dropdown = false;

    // --- CONTROLES DA UI ---
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $vehicleIdToDelete;
    public $vehiclePlateToDelete;
    public string $successMessage = '';
    public string $search = '';
    public string $filter = 'active';

    // --- PROPRIEDADES DO MODAL DE HISTÓRICO ---
    public $isHistoryModalOpen = false;
    public $vehicleForHistory = null;
    public string $historySearch = ''; // Propriedade para a busca no histórico

    public array $commonColors = ['PRETO', 'BRANCO', 'PRATA', 'CINZA', 'VERMELHO', 'AZUL', 'VERDE', 'AMARELO', 'DOURADO', 'MARROM', 'BEGE', 'LARANJA', 'ROXO'];

    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    // --- LÓGICA DE BUSCA DE MOTORISTA ---
    public function getFoundDriversProperty()
    {
        if (strlen(trim($this->driver_search)) < 2) {
            return collect();
        }
        return Driver::where('name', 'like', '%' . $this->driver_search . '%')
            ->withTrashed()
            ->take(5)
            ->get();
    }

    public function selectDriver($id, $name)
    {
        $this->driver_id = $id;
        $this->driver_search = $name;
        $this->show_driver_dropdown = false;
    }

    // --- MÉTODOS DE CICLO DE VIDA (LIFECYCLE HOOKS) ---
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ADICIONADO: Reseta a paginação do histórico ao buscar
    public function updatingHistorySearch()
    {
        $this->resetPage('historyPage');
    }

    // --- RENDERIZAÇÃO ---
    public function render()
    {
        // Lógica da lista principal de veículos
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

        // Lógica do histórico (COM BUSCA)
        $vehicleHistoryPaginator = null;
        if ($this->isHistoryModalOpen && $this->vehicleForHistory) {
            $this->vehicleForHistory->load('privateEntries.driver', 'officialTrips.driver');

            $privateEntries = $this->vehicleForHistory->privateEntries->map(fn($entry) => ['type' => 'Particular', 'start_time' => $entry->entry_at, 'end_time' => $entry->exit_at, 'driver_name' => $entry->driver->name ?? 'N/A', 'detail' => $entry->entry_reason]);
            $officialTrips = $this->vehicleForHistory->officialTrips->map(fn($trip) => ['type' => 'Oficial', 'start_time' => $trip->departure_datetime, 'end_time' => $trip->arrival_datetime, 'driver_name' => $trip->driver->name ?? 'N/A', 'detail' => $trip->destination]);

            $fullHistory = $privateEntries->concat($officialTrips)->sortByDesc('start_time');

            // Filtra o histórico se houver um termo de busca
            if (!empty($this->historySearch)) {
                $searchTerm = strtolower($this->historySearch);
                $fullHistory = $fullHistory->filter(function ($entry) use ($searchTerm) {
                    return str_contains(strtolower($entry['driver_name']), $searchTerm) ||
                        str_contains(strtolower($entry['detail']), $searchTerm) ||
                        str_contains(strtolower(\Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y')), $searchTerm);
                });
            }

            // Paginação
            $fullHistory = $fullHistory->values();
            $currentPage = LengthAwarePaginator::resolveCurrentPage('historyPage');
            $perPage = 5;
            $currentPageItems = $fullHistory->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $vehicleHistoryPaginator = new LengthAwarePaginator($currentPageItems, $fullHistory->count(), $perPage, $currentPage, ['path' => request()->url(), 'pageName' => 'historyPage']);
        }

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'vehicleHistory' => $vehicleHistoryPaginator,
        ]);
    }

    // --- AÇÕES DO CRUD ---
    public function store()
    {
        $this->validate([
            'license_plate' => ['required', 'string', 'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}-[0-9]{4}$/i', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|string|max:25',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:Oficial,Particular',
            'driver_id' => 'nullable|exists:drivers,id',
        ], ['license_plate.regex' => 'O formato da placa é inválido.']);

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => strtoupper($this->license_plate),
            'model'         => Str::upper($this->model),
            'color'         => Str::upper($this->color),
            'type'          => $this->type,
            'driver_id'     => $this->driver_id ?: null,
        ]);

        session()->flash('successMessage', $this->vehicleId ? 'Veículo atualizado!' : 'Veículo criado!');
        $this->closeModal();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $this->vehicleId = $id;
        $this->license_plate = $vehicle->license_plate;
        $this->model = $vehicle->model;
        $this->color = $vehicle->color;
        $this->type = $vehicle->type;
        $this->driver_id = $vehicle->driver_id;
        $this->driver_search = $vehicle->driver ? $vehicle->driver->name : '';

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['license_plate', 'model', 'color', 'vehicleId', 'type']);
        $this->resetErrorBag();
        $this->reset(['driver_id', 'driver_search', 'show_driver_dropdown']);
    }

    // --- MÉTODOS DE EXCLUSÃO E RESTAURAÇÃO ---
    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function deleteVehicle()
    {
        Vehicle::find($this->vehicleIdToDelete)->delete();
        session()->flash('successMessage', 'Veículo movido para a lixeira.');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    public function restore($id)
    {
        Vehicle::withTrashed()->find($id)->restore();
        session()->flash('successMessage', 'Veículo restaurado.');
    }

    public function confirmForceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function forceDeleteVehicle()
    {
        Vehicle::withTrashed()->find($this->vehicleIdToDelete)->forceDelete();
        session()->flash('successMessage', 'Veículo excluído permanentemente.');
        $this->closeConfirmModal();
    }

    // --- MÉTODOS DE HISTÓRICO ---
    public function showHistory($vehicleId)
    {
        $this->vehicleForHistory = Vehicle::withTrashed()->findOrFail($vehicleId);
        $this->resetPage('historyPage');
        $this->reset('historySearch'); // Limpa a busca ao abrir o modal
        $this->isHistoryModalOpen = true;
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->vehicleForHistory = null;
    }
}
