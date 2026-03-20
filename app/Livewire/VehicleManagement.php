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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class VehicleManagement extends Component
{
    use WithPagination;

    // --- SUAS PROPRIEDADES (sem alterações) ---
    public string $license_plate = '';
    public string $model = '';
    public string $color = '';
    public $vehicleId;
    public string $type = 'Particular';
    public array $selected_drivers = [];
    public string $driver_search = '';
    public bool $show_driver_dropdown = false;
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $vehicleIdToDelete;
    public $vehiclePlateToDelete;
    public string $successMessage = '';
    public string $search = '';
    public string $filter = 'active';
    public $isHistoryModalOpen = false;
    public $vehicleForHistory = null;
    public string $historySearch = '';
    public array $commonColors = ['PRETO', 'BRANCO', 'PRATA', 'CINZA', 'VERMELHO', 'AZUL', 'VERDE', 'AMARELO', 'DOURADO', 'MARROM', 'BEGE', 'LARANJA', 'ROXO'];
    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    public function mount()
    {
        $user = Auth::user();

        // Se for um fiscal oficial, a tela já deve carregar com o tipo 'Oficial' selecionado internamente
        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            $this->type = 'Oficial';
        }
    }

    // --- LÓGICA DE BUSCA DE MOTORISTA ---
    public function getFoundDriversProperty()
    {
        $search = trim($this->driver_search);

        if (strlen($search) < 2) {
            return collect();
        }

        $cleanSearch = preg_replace('/\D/', '', $search);

        return Driver::where('name', 'like', '%' . $search . '%')
            ->when(strlen($cleanSearch) > 0, function ($query) use ($cleanSearch) {
                $query->orWhere('document', 'like', '%' . $cleanSearch . '%');
            })
            ->orderBy('name')
            ->take(5)
            ->get();
    }

    public function selectDriver($id, $name)
    {
        $exists = collect($this->selected_drivers)->contains('id', $id);

        if (!$exists) {
            $this->selected_drivers[] = ['id' => $id, 'name' => $name];
        }

        $this->driver_search = '';
        $this->show_driver_dropdown = false;
    }

    public function removeDriver($id)
    {
        $this->selected_drivers = collect($this->selected_drivers)
            ->reject(fn($driver) => $driver['id'] === $id)
            ->values()
            ->toArray();
    }

    // --- MÉTODOS DE CICLO DE VIDA ---
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingHistorySearch()
    {
        $this->resetPage('historyPage');
    }

    public function canManageVehicle(Vehicle $vehicle = null, string $requestedType = null): bool
    {
        $user = Auth::user();
        $targetType = $vehicle ? $vehicle->type : $requestedType;

        if (!$targetType) return false;
        if ($user->role === 'admin') return true;

        if ($user->role === 'fiscal') {
            if (!$user->fiscal_type) return false;

            // O Fiscal Oficial só mexe na Frota Oficial
            if ($user->fiscal_type === 'official' && $targetType === 'Oficial') return true;

            // O Fiscal Particular (DIRETOR ADMIN) pode mexer em TUDO (Oficial e Particular)
            if ($user->fiscal_type === 'private' || $user->fiscal_type === 'both') return true;
        }

        if ($user->role === 'porteiro') {
            if ($targetType === 'Particular') return true;
        }

        return false;
    }

    public function canCreateVehicle(): bool
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'porteiro'])) {
            return true;
        }

        if ($user->role === 'fiscal' && in_array($user->fiscal_type, ['official', 'private', 'both'])) {
            return true;
        }

        return false;
    }


    // --- RENDERIZAÇÃO (COM FILTRO E HISTÓRICO APRIMORADO) ---
    public function render()
    {
        $query = Vehicle::with('drivers');
        $user = Auth::user();

        // Se for Fiscal Oficial, vê só oficial. Se for Particular (Diretor), VÊ TUDO!
        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'official') {
                $query->where('type', 'Oficial');
            }
        } elseif ($user->role === 'porteiro') {
            $query->where('type', 'Particular');
        }

        if ($this->filter === 'trashed') {
            $query->onlyTrashed();
            if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
                $query->where('type', 'Oficial');
            }
            if ($user->role === 'porteiro') {
                $query->where('type', 'Particular');
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('license_plate', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('color', 'like', $searchTerm)
                    ->$subQuery->orWhereHas('drivers', function ($driverQuery) use ($searchTerm) {
                        $driverQuery->where('name', 'like', $searchTerm);
                    });
            });
        }
        $vehicles = $query->orderBy('model', 'asc')->paginate(10);

        // ========================================================
        // NOVA LÓGICA DE HISTÓRICO (Com Odômetro, Porteiros, etc)
        // ========================================================
        $vehicleHistoryPaginator = null;
        if ($this->isHistoryModalOpen && $this->vehicleForHistory) {

            // Busca de Particulares com colunas nulas para parear com as Oficiais
            $private = DB::table('private_entries')
                ->join('drivers', 'private_entries.driver_id', '=', 'drivers.id')
                ->leftJoin('users as guard_in', 'private_entries.guard_on_entry_id', '=', 'guard_in.id')
                ->leftJoin('users as guard_out', 'private_entries.guard_on_exit_id', '=', 'guard_out.id')
                ->select(
                    DB::raw("'Particular' as type"),
                    'private_entries.entry_at as start_time',
                    'private_entries.exit_at as end_time',
                    'drivers.name as driver_name',
                    'private_entries.entry_reason as detail',
                    DB::raw("NULL as departure_odometer"),
                    DB::raw("NULL as arrival_odometer"),
                    DB::raw("NULL as distance_traveled"),
                    DB::raw("NULL as passengers"),
                    DB::raw("NULL as return_observation"),
                    'guard_in.name as guard_start',
                    'guard_out.name as guard_end'
                )
                ->where('private_entries.vehicle_id', $this->vehicleForHistory->id);


            // Busca de Oficiais
            $official = DB::table('official_trips')
                ->join('drivers', 'official_trips.driver_id', '=', 'drivers.id')
                ->leftJoin('users as guard_out', 'official_trips.guard_on_departure_id', '=', 'guard_out.id')
                ->leftJoin('users as guard_in', 'official_trips.guard_on_arrival_id', '=', 'guard_in.id')
                ->select(
                    DB::raw("'Oficial' as type"),
                    'official_trips.departure_datetime as start_time',
                    'official_trips.arrival_datetime as end_time',
                    'drivers.name as driver_name',
                    'official_trips.destination as detail',
                    'official_trips.departure_odometer',
                    'official_trips.arrival_odometer',
                    // CORREÇÃO: O Banco de dados faz a conta da distância na hora da busca
                    DB::raw('(official_trips.arrival_odometer - official_trips.departure_odometer) as distance_traveled'),
                    'official_trips.passengers',
                    'official_trips.return_observation',
                    'guard_out.name as guard_start',
                    'guard_in.name as guard_end'
                )
                ->where('official_trips.vehicle_id', $this->vehicleForHistory->id);

            if (!empty($this->historySearch)) {
                $searchTerm = '%' . $this->historySearch . '%';
                $private->where(fn($q) => $q->where('drivers.name', 'like', $searchTerm)->orWhere('private_entries.entry_reason', 'like', $searchTerm));
                $official->where(fn($q) => $q->where('drivers.name', 'like', $searchTerm)->orWhere('official_trips.destination', 'like', $searchTerm));
            }

            $vehicleHistoryPaginator = $private->unionAll($official)
                ->orderBy('start_time', 'desc')
                ->paginate(5, ['*'], 'historyPage');
        }

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'vehicleHistory' => $vehicleHistoryPaginator,
        ]);
    }

    // --- AÇÕES DO CRUD ---
    public function store()
    {
        if (!$this->canManageVehicle(requestedType: $this->type)) {
            abort(403, 'Você não tem permissão para criar ou editar este tipo de veículo.');
        }

        $validatedData = $this->validate([
            'license_plate' => ['required', 'string', 'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}-[0-9]{4}$/i', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|string|max:25',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:Oficial,Particular',
        ], ['license_plate.regex' => 'O formato da placa é inválido.']);

        $vehicle = Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => strtoupper($validatedData['license_plate']),
            'model'         => Str::upper($validatedData['model']),
            'color'         => Str::upper($validatedData['color']),
            'type'          => $validatedData['type'],
        ]);

        if ($validatedData['type'] === 'Particular' && count($this->selected_drivers) > 0) {
            $driverIds = array_column($this->selected_drivers, 'id');
            $vehicle->drivers()->sync($driverIds);
        } else {
            $vehicle->drivers()->detach();
        }

        session()->flash('successMessage', $this->vehicleId ? 'Veículo atualizado!' : 'Veículo criado!');
        $this->closeModal();
    }
    public function create()
    {
        if (!in_array(Auth::user()->role, ['admin', 'porteiro', 'fiscal'])) {
            abort(403);
        }
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $vehicle = Vehicle::withTrashed()->findOrFail($id);

        if (!$this->canManageVehicle($vehicle)) {
            session()->flash('error', 'Você não tem permissão para editar este veículo.');
            return;
        }

        $this->vehicleId = $id;
        $this->license_plate = $vehicle->license_plate;
        $this->model = $vehicle->model;
        $this->color = $vehicle->color;
        $this->type = $vehicle->type;

        $this->selected_drivers = $vehicle->drivers->map(function ($d) {
            return ['id' => $d->id, 'name' => $d->name];
        })->toArray();
        $this->driver_search = '';

        $this->isModalOpen = true;
    }

    private function resetInputFields()
    {
        $user = Auth::user();
        $defaultType = 'Particular';
        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'official') $defaultType = 'Oficial';
        }
        $this->reset(['license_plate', 'model', 'color', 'vehicleId', 'driver_search', 'show_driver_dropdown', 'selected_drivers']);
        $this->type = $defaultType;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    // --- MÉTODOS DE EXCLUSÃO ---
    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if (!$this->canManageVehicle($vehicle)) {
            session()->flash('error', 'Ação não autorizada.');
            return;
        }
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function deleteVehicle()
    {
        $vehicle = Vehicle::findOrFail($this->vehicleIdToDelete);
        if (!$this->canManageVehicle($vehicle)) {
            $this->closeConfirmModal();
            session()->flash('error', '...');
            return;
        }
        $vehicle->delete();
        session()->flash('successMessage', 'Veículo movido para a lixeira.');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->reset(['vehicleIdToDelete', 'vehiclePlateToDelete']);
    }

    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        if (!$this->canManageVehicle($vehicle)) {
            session()->flash('error', 'Ação não autorizada.');
            return;
        }
        $vehicle->restore();
        session()->flash('successMessage', 'Veículo restaurado.');
    }

    public function confirmForceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        if (!$this->canManageVehicle($vehicle)) {
            session()->flash('error', 'Ação não autorizada.');
            return;
        }
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function forceDeleteVehicle()
    {
        $vehicle = Vehicle::withTrashed()->find($this->vehicleIdToDelete);
        if (!$vehicle) {
            session()->flash('error', 'Veículo não encontrado.');
            $this->closeConfirmModal();
            return;
        }
        if (!$this->canManageVehicle($vehicle)) {
            $this->closeConfirmModal();
            session()->flash('error', '...');
            return;
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

    // --- MÉTODOS DE HISTÓRICO ---
    public function showHistory($vehicleId)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($vehicleId);
        if (!$this->canManageVehicle($vehicle)) {
            session()->flash('error', 'Você não tem permissão para ver o histórico deste veículo.');
            return;
        }
        $this->vehicleForHistory = $vehicle;
        $this->resetPage('historyPage');
        $this->reset('historySearch');
        $this->isHistoryModalOpen = true;
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->vehicleForHistory = null;
        $this->reset('historySearch');
    }
}
