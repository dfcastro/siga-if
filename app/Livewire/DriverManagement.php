<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Rules\Cpf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class DriverManagement extends Component
{
    use WithPagination;

    // --- PROPRIEDADES ---
    public string $name = '';
    public string $document = '';
    public string $type = 'Servidor';
    public $driverId;
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $driverIdToDelete;
    public $driverNameToDelete;
    public bool $is_authorized = true;
    public string $search = '';
    public ?string $telefone = null;
    public string $historySearch = '';
    public $isHistoryModalOpen = false;
    public $driverForHistory = null;
    public string $filter = 'active';

    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Motoristas'];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingHistorySearch()
    {
        $this->resetPage('historyPage');
    }

    // ### FUNÇÃO AUXILIAR PARA VERIFICAR PERMISSÃO ###
    public function canManageDriver(Driver $driver = null): bool
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return true;
        }
        if ($user->role === 'fiscal' && !$user->fiscal_type) {
            return false;
        }
        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            return $driver ? $driver->is_authorized : true;
        }
        if ($user->role === 'fiscal' && in_array($user->fiscal_type, ['private', 'both'])) {
            return true;
        }
        if ($user->role === 'porteiro') {
            return $driver ? !$driver->is_authorized : true;
        }

        return false;
    }

    // --- RENDERIZAÇÃO ---
    public function render()
    {
        $query = Driver::query();
        $user = Auth::user();

        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            $query->where('is_authorized', true);
        }

        if ($this->filter === 'trashed') {
            $query->onlyTrashed();
            if ($user->role === 'porteiro') {
                $query->where('is_authorized', false);
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('name', 'like', $searchTerm)
                    ->orWhere('document', 'like', $searchTerm);
            });
        }

        $drivers = $query->orderBy('name', 'asc')->paginate(10);

        // ========================================================
        // HISTÓRICO BLINDADO (Com CAST para evitar truncamento no MySQL)
        // ========================================================
        $driverHistoryPaginator = null;

        if ($this->isHistoryModalOpen && $this->driverForHistory) {

            // 1. Viagens Oficiais
            $official = DB::table('official_trips')
                ->leftJoin('vehicles', 'official_trips.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('users as guard_out', 'official_trips.guard_on_departure_id', '=', 'guard_out.id')
                ->leftJoin('users as guard_in', 'official_trips.guard_on_arrival_id', '=', 'guard_in.id')
                ->select(
                    DB::raw("'Oficial' as type"),
                    'official_trips.departure_datetime as start_time',
                    'official_trips.arrival_datetime as end_time',
                    DB::raw("COALESCE(CONCAT(vehicles.model, ' (', vehicles.license_plate, ')'), 'Veículo não informado') as vehicle_info"),
                    'official_trips.destination as detail',
                    'official_trips.departure_odometer',
                    'official_trips.arrival_odometer',
                    DB::raw('(official_trips.arrival_odometer - official_trips.departure_odometer) as distance_traveled'),
                    'official_trips.passengers',
                    'official_trips.return_observation',
                    'guard_out.name as guard_start',
                    'guard_in.name as guard_end'
                )
                ->where('official_trips.driver_id', $this->driverForHistory->id);

            // 2. Entradas Particulares
            $private = DB::table('private_entries')
                ->leftJoin('vehicles', 'private_entries.vehicle_id', '=', 'vehicles.id')
                ->leftJoin('users as guard_in', 'private_entries.guard_on_entry_id', '=', 'guard_in.id')
                ->leftJoin('users as guard_out', 'private_entries.guard_on_exit_id', '=', 'guard_out.id')
                ->select(
                    DB::raw("'Particular' as type"),
                    'private_entries.entry_at as start_time',
                    'private_entries.exit_at as end_time',
                    DB::raw("COALESCE(CONCAT(vehicles.model, ' (', vehicles.license_plate, ')'), 'Veículo não informado') as vehicle_info"),
                    'private_entries.entry_reason as detail',
                    DB::raw("CAST(NULL AS UNSIGNED) as departure_odometer"),
                    DB::raw("CAST(NULL AS UNSIGNED) as arrival_odometer"),
                    DB::raw("CAST(NULL AS UNSIGNED) as distance_traveled"),
                    DB::raw("CAST(NULL AS CHAR(255)) as passengers"),
                    DB::raw("CAST(NULL AS CHAR(255)) as return_observation"), // Força o MySQL a saber que é texto
                    'guard_in.name as guard_start',
                    'guard_out.name as guard_end'
                )
                ->where('private_entries.driver_id', $this->driverForHistory->id);

            if (!empty($this->historySearch)) {
                $searchTerm = '%' . $this->historySearch . '%';

                $official->where(fn($q) => $q->where('vehicles.license_plate', 'like', $searchTerm)
                    ->orWhere('vehicles.model', 'like', $searchTerm) // <-- Adicionado Modelo
                    ->orWhere('official_trips.destination', 'like', $searchTerm));

                $private->where(fn($q) => $q->where('vehicles.license_plate', 'like', $searchTerm)
                    ->orWhere('vehicles.model', 'like', $searchTerm) // <-- Adicionado Modelo
                    ->orWhere('private_entries.entry_reason', 'like', $searchTerm));
            }

            // O Oficial DEVE vir primeiro no UNION
            $driverHistoryPaginator = $official->unionAll($private)
                ->orderBy('start_time', 'desc')
                ->paginate(5, ['*'], 'historyPage');
        }

        return view('livewire.driver-management', [
            'drivers' => $drivers,
            'driverHistory' => $driverHistoryPaginator,
        ]);
    }

    public function showHistory($driverId)
    {
        $driver = Driver::withTrashed()->findOrFail($driverId);
        $user = Auth::user();

        // O Porteiro pode VER o histórico, mesmo que não possa editar o motorista
        $canView = $this->canManageDriver($driver) || $user->role === 'porteiro';

        if (!$canView) {
            session()->flash('errorMessage', 'Você não tem permissão para ver o histórico deste motorista.');
            return;
        }

        $this->driverForHistory = $driver;
        $this->resetPage('historyPage');
        $this->isHistoryModalOpen = true;
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->driverForHistory = null;
        $this->reset('historySearch');
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:50',
            'document' => ['required', new Cpf, Rule::unique('drivers')->ignore($this->driverId)],
            'telefone' => 'nullable|string|max:20',
            'type' => 'required|in:Servidor,Aluno,Terceirizado,Visitante',
            'is_authorized' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value === true && in_array($this->type, ['Aluno', 'Visitante'])) {
                        $fail('Apenas motoristas do tipo Servidor ou Terceirizado podem ser autorizados para a frota oficial.');
                    }
                },
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.max' => 'O nome não pode ter mais de 100 caracteres.',
        'document.required' => 'O campo documento é obrigatório.',
        'document.unique' => 'Este documento já está cadastrado.',
        'type.required' => 'O campo tipo é obrigatório.',
        'type.in' => 'O tipo selecionado é inválido.',
    ];

    public function store()
    {
        $user = Auth::user();

        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            $this->is_authorized = true;
        } elseif ($user->role === 'porteiro' || ($user->role === 'fiscal' && $user->fiscal_type === 'private')) {
            $this->is_authorized = false;
        }

        $this->document = preg_replace('/\D/', '', $this->document);

        if (!$this->driverId && !empty($this->document)) {
            $existingDriver = Driver::withTrashed()->where('document', $this->document)->first();

            if ($existingDriver) {
                $isFiscalOficial = $user->role === 'fiscal' && $user->fiscal_type === 'official';

                if ($isFiscalOficial && in_array($existingDriver->type, ['Servidor', 'Terceirizado'])) {
                    $this->driverId = $existingDriver->id;
                } elseif ($user->role === 'admin') {
                    $this->driverId = $existingDriver->id;
                } else {
                    $statusLixeira = $existingDriver->trashed() ? ' (na Lixeira)' : '';
                    $this->addError('document', "Este CPF já pertence a: {$existingDriver->name} - {$existingDriver->type}{$statusLixeira}. Para transformar um Visitante/Aluno em condutor oficial, contate o Administrador.");
                    return;
                }
            }
        }

        $validatedData = $this->validate();
        $driver = $this->driverId ? Driver::withTrashed()->find($this->driverId) : null;

        $isPromoting = false;
        if ($user->role === 'fiscal' && $user->fiscal_type === 'official' && $driver && in_array($driver->type, ['Servidor', 'Terceirizado'])) {
            $isPromoting = true;
        }

        if (!$isPromoting && !$this->canManageDriver($driver)) {
            $this->addError('document', 'Você não tem permissão para gerenciar este motorista.');
            return;
        }

        if ($driver) {
            $driver->update([
                'name' => Str::title($validatedData['name']),
                'document' => $validatedData['document'],
                'telefone' => $validatedData['telefone'],
                'type' => $validatedData['type'],
                'is_authorized' => $validatedData['is_authorized'],
            ]);

            if ($driver->trashed()) {
                $driver->restore();
            }
        } else {
            Driver::create([
                'name' => Str::title($validatedData['name']),
                'document' => $validatedData['document'],
                'telefone' => $validatedData['telefone'],
                'type' => $validatedData['type'],
                'is_authorized' => $validatedData['is_authorized'],
            ]);
        }

        session()->flash('success', $isPromoting ? 'Servidor atualizado e autorizado para a frota oficial!' : ($this->driverId ? 'Motorista atualizado!' : 'Motorista cadastrado!'));
        $this->closeModal();
    }

    public function create()
    {
        if (!in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro'])) {
            abort(403, 'Ação não autorizada.');
        }
        $this->resetInputFields();
        if (Auth::user()->role === 'fiscal' && Auth::user()->fiscal_type === 'official') {
            $this->is_authorized = true;
            $this->type = 'Servidor';
        }
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);
        if (!$this->canManageDriver($driver)) {
            session()->flash('errorMessage', 'Você não tem permissão para editar este motorista.');
            return;
        }
        $this->driverId = $id;
        $this->name = $driver->name;
        $this->document = $driver->document;
        $this->telefone = $driver->telefone;
        $this->type = $driver->type;
        $this->is_authorized = $driver->is_authorized;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'document', 'type', 'driverId', 'is_authorized', 'telefone']);

        $user = Auth::user();

        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            $this->type = 'Servidor';
            $this->is_authorized = true;
        } else {
            $this->type = 'Visitante';
            $this->is_authorized = false;
        }

        $this->resetErrorBag();
    }

    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);

        if (!$this->canManageDriver($driver)) {
            session()->flash('errorMessage', 'Ação não autorizada.');
            return;
        }

        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    public function deleteDriver()
    {
        $driver = Driver::find($this->driverIdToDelete);

        if (!$driver || !$this->canManageDriver($driver)) {
            $this->closeConfirmModal();
            session()->flash('errorMessage', 'Ação não autorizada ou motorista não encontrado.');
            return;
        }

        $driver->delete();
        session()->flash('success', 'Motorista movido para a lixeira com sucesso!');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->reset(['driverIdToDelete', 'driverNameToDelete']);
    }

    public function restore($id)
    {
        $driver = Driver::withTrashed()->find($id);

        if (!$driver || !$this->canManageDriver($driver)) {
            session()->flash('errorMessage', 'Ação não autorizada ou motorista não encontrado.');
            return;
        }

        $driver->restore();
        session()->flash('success', 'Motorista restaurado com sucesso!');
    }

    public function confirmForceDelete($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);

        if (!$this->canManageDriver($driver)) {
            session()->flash('errorMessage', 'Ação não autorizada.');
            return;
        }

        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    public function forceDeleteDriver()
    {
        $driver = Driver::withTrashed()->find($this->driverIdToDelete);

        if (!$driver) {
            session()->flash('errorMessage', 'Motorista não encontrado.');
            $this->closeConfirmModal();
            return;
        }

        if (!$this->canManageDriver($driver)) {
            $this->closeConfirmModal();
            session()->flash('errorMessage', 'Ação não autorizada.');
            return;
        }

        if ($driver->officialTrips()->exists()) {
            session()->flash('errorMessage', 'Não é possível excluir permanentemente. O motorista possui um histórico de viagens oficiais.');
            $this->closeConfirmModal();
            return;
        }
        if ($driver->privateEntries()->exists()) {
            session()->flash('errorMessage', 'Não é possível excluir permanentemente. O motorista possui um histórico de entradas particulares.');
            $this->closeConfirmModal();
            return;
        }
        if ($driver->vehicles()->exists()) {
            session()->flash('errorMessage', 'Não é possível excluir permanentemente. O motorista ainda possui veículos associados.');
            $this->closeConfirmModal();
            return;
        }

        $driver->forceDelete();
        session()->flash('success', 'Motorista excluído permanentemente.');
        $this->closeConfirmModal();
    }
}
