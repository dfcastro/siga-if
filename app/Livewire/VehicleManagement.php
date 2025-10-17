<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB; // É importante importar a facade DB

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
    public string $historySearch = '';

    public array $commonColors = ['PRETO', 'BRANCO', 'PRATA', 'CINZA', 'VERMELHO', 'AZUL', 'VERDE', 'AMARELO', 'DOURADO', 'MARROM', 'BEGE', 'LARANJA', 'ROXO'];
    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    // --- LÓGICA DE BUSCA DE MOTORISTA (SEM ALTERAÇÃO) ---
    public function getFoundDriversProperty()
    {
        if (strlen(trim($this->driver_search)) < 2) {
            return collect();
        }
        return Driver::where('name', 'like', '%' . $this->driver_search . '%')
            ->take(5)
            ->get();
    }

    public function selectDriver($id, $name)
    {
        $this->driver_id = $id;
        $this->driver_search = $name;
        $this->show_driver_dropdown = false;
    }

    // --- MÉTODOS DE CICLO DE VIDA (SEM ALTERAÇÃO) ---
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingHistorySearch()
    {
        $this->resetPage('historyPage');
    }

    // --- RENDERIZAÇÃO (COM LÓGICA DE HISTÓRICO OTIMIZADA) ---
    public function render()
    {
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

        $vehicleHistoryPaginator = null;
        if ($this->isHistoryModalOpen && $this->vehicleForHistory) {
            $private = DB::table('private_entries')->join('drivers', 'private_entries.driver_id', '=', 'drivers.id')->select(DB::raw("'Particular' as type"), 'private_entries.entry_at as start_time', 'private_entries.exit_at as end_time', 'drivers.name as driver_name', 'private_entries.entry_reason as detail')->where('private_entries.vehicle_id', $this->vehicleForHistory->id);
            $official = DB::table('official_trips')->join('drivers', 'official_trips.driver_id', '=', 'drivers.id')->select(DB::raw("'Oficial' as type"), 'official_trips.departure_datetime as start_time', 'official_trips.arrival_datetime as end_time', 'drivers.name as driver_name', 'official_trips.destination as detail')->where('official_trips.vehicle_id', $this->vehicleForHistory->id);

            if (!empty($this->historySearch)) {
                $searchTerm = '%' . $this->historySearch . '%';
                $private->where(fn($q) => $q->where('drivers.name', 'like', $searchTerm)->orWhere('private_entries.entry_reason', 'like', $searchTerm));
                $official->where(fn($q) => $q->where('drivers.name', 'like', $searchTerm)->orWhere('official_trips.destination', 'like', $searchTerm));
            }

            $vehicleHistoryPaginator = $private->unionAll($official)->orderBy('start_time', 'desc')->paginate(5, ['*'], 'historyPage');
        }

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'vehicleHistory' => $vehicleHistoryPaginator,
        ]);
    }

    // --- AÇÕES DO CRUD COM AS NOVAS REGRAS DE PERMISSÃO ---
    public function store()
    {
        $userRole = auth()->user()->role;

        // **NOVA REGRA DE VALIDAÇÃO DE PERMISSÃO**
        if (
            ($this->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($this->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Você não tem permissão para criar ou editar este tipo de veículo.');
        }

        $this->validate([
            'license_plate' => ['required', 'string', 'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}-[0-9]{4}$/i', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|string|max:25',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:Oficial,Particular',
            'driver_id' => $this->type === 'Particular' ? 'nullable|exists:drivers,id' : '',
        ], ['license_plate.regex' => 'O formato da placa é inválido.']);

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => strtoupper($this->license_plate),
            'model'         => Str::upper($this->model),
            'color'         => Str::upper($this->color),
            'type'          => $this->type,
            'driver_id'     => $this->type === 'Particular' ? ($this->driver_id ?: null) : null,
        ]);

        session()->flash('successMessage', $this->vehicleId ? 'Veículo atualizado!' : 'Veículo criado!');
        $this->closeModal();
    }

    public function create()
    {
        if (!in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal'])) {
            abort(403, 'Ação não autorizada.');
        }
        $this->resetInputFields();

        if (auth()->user()->role === 'fiscal') {
            $this->type = 'Oficial';
        } else {
            $this->type = 'Particular';
        }

        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $userRole = auth()->user()->role;

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Você não tem permissão para editar este veículo.');
        }

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

    // --- MÉTODOS DE EXCLUSÃO COM PERMISSÕES E SEGURANÇA ---
    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $userRole = auth()->user()->role;

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Ação não autorizada.');
        }

        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function deleteVehicle()
    {
        $vehicle = Vehicle::findOrFail($this->vehicleIdToDelete);
        $userRole = auth()->user()->role;

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Você não tem permissão para mover este veículo para a lixeira.');
        }

        $vehicle->delete();
        session()->flash('successMessage', 'Veículo movido para a lixeira.');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $userRole = auth()->user()->role;

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Ação não autorizada.');
        }

        $vehicle->restore();
        session()->flash('successMessage', 'Veículo restaurado.');
    }

    public function confirmForceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $userRole = auth()->user()->role;

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Ação não autorizada.');
        }

        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function forceDeleteVehicle()
    {
        $vehicle = Vehicle::withTrashed()->find($this->vehicleIdToDelete);
        $userRole = auth()->user()->role;

        if (!$vehicle) {
            session()->flash('error', 'Veículo não encontrado.');
            $this->closeConfirmModal();
            return;
        }

        if (
            ($vehicle->type === 'Oficial' && !in_array($userRole, ['admin', 'fiscal'])) ||
            ($vehicle->type === 'Particular' && !in_array($userRole, ['admin', 'porteiro']))
        ) {
            abort(403, 'Você não tem permissão para excluir este veículo permanentemente.');
        }

        if ($vehicle->officialTrips()->exists() || $vehicle->privateEntries()->exists()) {
            session()->flash('error', 'Não é possível excluir: o veículo possui registros associados.');
            $this->closeConfirmModal();
            return;
        }

        $vehicle->forceDelete();
        session()->flash('successMessage', 'Veículo excluído permanentemente.');
        $this->closeConfirmModal();
    }

    // --- MÉTODOS DE HISTÓRICO (SEM ALTERAÇÃO) ---
    public function showHistory($vehicleId)
    {
        $this->vehicleForHistory = Vehicle::withTrashed()->findOrFail($vehicleId);
        $this->resetPage('historyPage');
        $this->reset('historySearch');
        $this->isHistoryModalOpen = true;
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->vehicleForHistory = null;
    }
}
