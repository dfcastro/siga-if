<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Rules\Cpf;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class DriverManagement extends Component
{
    use WithPagination;

    // Suas propriedades existentes
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

    // PROPRIEDADE PARA A BUSCA NO HISTÓRICO
    public string $historySearch = '';

    // Propriedades para o modal de histórico
    public $isHistoryModalOpen = false;
    public $driverForHistory = null;

    // Propriedade para o filtro de visualização
    public string $filter = 'active'; // 'active' ou 'trashed'

    protected $paginationTheme = 'tailwind';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Motoristas'];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Driver::query();

        if ($this->filter === 'trashed') {
            $query->onlyTrashed();
        }

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('name', 'like', $searchTerm)
                    ->orWhere('document', 'like', $searchTerm);
            });
        }

        $drivers = $query->orderBy('name', 'asc')->paginate(10);

        // Lógica do Histórico (com busca)
        $driverHistoryPaginator = null;
        if ($this->isHistoryModalOpen && $this->driverForHistory) {
            $this->driverForHistory->load('privateEntries.vehicle', 'officialTrips.vehicle');

            $privateEntries = $this->driverForHistory->privateEntries->map(function ($entry) {
                return [
                    'type' => 'Particular',
                    'start_time' => $entry->entry_at,
                    'end_time' => $entry->exit_at,
                    'vehicle_info' => $entry->vehicle ? "{$entry->vehicle->model} ({$entry->vehicle->license_plate})" : 'Veículo Removido',
                    'detail' => $entry->entry_reason,
                ];
            });

            $officialTrips = $this->driverForHistory->officialTrips->map(function ($trip) {
                return [
                    'type' => 'Oficial',
                    'start_time' => $trip->departure_datetime,
                    'end_time' => $trip->arrival_datetime,
                    'vehicle_info' => $trip->vehicle ? "{$trip->vehicle->model} ({$trip->vehicle->license_plate})" : 'Veículo Removido',
                    'detail' => $trip->destination,
                ];
            });

            $fullHistory = $privateEntries->concat($officialTrips)->sortByDesc('start_time');

            // Filtra a coleção do histórico se houver um termo de busca
            if (!empty($this->historySearch)) {
                $searchTerm = strtolower($this->historySearch);
                $fullHistory = $fullHistory->filter(function ($entry) use ($searchTerm) {
                    return str_contains(strtolower($entry['vehicle_info']), $searchTerm) ||
                        str_contains(strtolower($entry['detail']), $searchTerm) ||
                        str_contains(strtolower(\Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y')), $searchTerm);
                });
            }

            // Pagina a coleção resultante
            $fullHistory = $fullHistory->values();
            $currentPage = LengthAwarePaginator::resolveCurrentPage('historyPage');
            $perPage = 5;
            $currentPageItems = $fullHistory->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $driverHistoryPaginator = new LengthAwarePaginator($currentPageItems, $fullHistory->count(), $perPage, $currentPage, [
                'path' => request()->url(),
                'pageName' => 'historyPage',
            ]);
        }

        return view('livewire.driver-management', [
            'drivers' => $drivers,
            'driverHistory' => $driverHistoryPaginator,
        ]);
    }

    /**
     *  Método para abrir o modal de histórico.
     */
    public function showHistory($driverId)
    {
        $this->driverForHistory = Driver::withTrashed()->findOrFail($driverId);
        $this->resetPage('historyPage');
        $this->isHistoryModalOpen = true;
    }

    /**
     * ADICIONADO: Método para fechar o modal de histórico.
     */
    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
        $this->driverForHistory = null;
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            'document' => ['required', new Cpf, Rule::unique('drivers')->ignore($this->driverId)],
            'type' => 'required',
            'is_authorized' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.max' => 'O nome não pode ter mais de 100 caracteres.',
        'document.required' => 'O campo documento é obrigatório.',
        'document.unique' => 'Este documento já está cadastrado.',
        'type.required' => 'O campo tipo é obrigatório.',
    ];

    public function store()
    {
        $this->validate();

        Driver::updateOrCreate(['id' => $this->driverId], [
            'name' => Str::title($this->name),
            'document' => $this->document,
            'type' => $this->type,
            'is_authorized' => $this->is_authorized,
        ]);

        session()->flash('success', $this->driverId ? 'Motorista atualizado!' : 'Motorista cadastrado!');
        $this->closeModal();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        // ALTERADO: Busca o motorista mesmo que esteja na lixeira para permitir edição antes de restaurar, se necessário.
        $driver = Driver::withTrashed()->findOrFail($id);
        $this->driverId = $id;
        $this->name = $driver->name;
        $this->document = $driver->document;
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
        $this->reset(['name', 'document', 'type', 'driverId', 'is_authorized']);
        $this->type = 'Servidor';
        $this->is_authorized = true;
        $this->resetErrorBag();
    }

    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    // ALTERADO: Este método agora faz o "soft delete"
    public function deleteDriver()
    {
        Driver::find($this->driverIdToDelete)->delete(); // Isto agora move para a lixeira
        session()->flash('success', 'Motorista movido para a lixeira com sucesso!');
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    // ADICIONADO: Método para restaurar
    public function restore($id)
    {
        Driver::withTrashed()->find($id)->restore();
        session()->flash('success', 'Motorista restaurado com sucesso!');
    }

    // ADICIONADO: Método para confirmar a exclusão permanente
    public function confirmForceDelete($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);
        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    // ADICIONADO: Método para apagar permanentemente
    public function forceDeleteDriver()
    {
        Driver::withTrashed()->find($this->driverIdToDelete)->forceDelete();
        session()->flash('success', 'Motorista excluído permanentemente!');
        $this->closeConfirmModal();
    }
}
