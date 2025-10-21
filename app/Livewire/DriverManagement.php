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
use Illuminate\Support\Facades\Auth; // Importar Auth

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
    public ?string $telefone = null; // Corrigido para permitir null
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

        // Admin pode gerenciar todos
        if ($user->role === 'admin') {
            return true;
        }

        // Fiscal só pode gerenciar motoristas se tiver fiscal_type definido
        if ($user->role === 'fiscal' && !$user->fiscal_type) {
            return false;
        }

        // Fiscal de oficial SÓ PODE gerenciar motoristas AUTORIZADOS
        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            return $driver ? $driver->is_authorized : true; // Permite criar (driver é null), mas só edita/exclui se for autorizado
        }

        // Fiscal de particular ou ambos podem gerenciar qualquer motorista
        if ($user->role === 'fiscal' && in_array($user->fiscal_type, ['private', 'both'])) {
            return true;
        }

        // Porteiro SÓ PODE gerenciar motoristas NÃO AUTORIZADOS
        if ($user->role === 'porteiro') {
            // Se $driver existe, verifica se ele NÃO É autorizado.
            // Se $driver não existe (criação), permite abrir o modal, a restrição será no store().
            return $driver ? !$driver->is_authorized : true;
        }

        return false; // Nega por padrão
    }


    // --- RENDERIZAÇÃO (COM FILTRO DE PERMISSÃO) ---
    public function render()
    {
        $query = Driver::query();
        $user = Auth::user();

        if ($user->role === 'fiscal' && $user->fiscal_type === 'official') {
            $query->where('is_authorized', true);
        }
        // ### NOVO FILTRO PARA PORTEIRO NA LISTAGEM ###
        // O Porteiro também não precisa ver motoristas autorizados na lista principal? (Opcional)
        // Se quiser esconder os autorizados da lista do porteiro, descomente a linha abaixo:
        // elseif ($user->role === 'porteiro') {
        //     $query->where('is_authorized', false);
        // }


        if ($this->filter === 'trashed') {
            $query->onlyTrashed();
            // Na lixeira, o porteiro PODE ver autorizados para restaurar? Ou não?
            // Se não puder nem ver na lixeira, adicione o filtro aqui também.
            if ($user->role === 'porteiro') {
                $query->where('is_authorized', false); // Impede porteiro de ver autorizados na lixeira
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

        // ... (Lógica do Histórico mantida)
        $driverHistoryPaginator = null;
        if ($this->isHistoryModalOpen && $this->driverForHistory) {
            $this->driverForHistory->load('privateEntries.vehicle', 'officialTrips.vehicle');
            // ... (código map/filter/paginate do histórico) ...
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
            if (!empty($this->historySearch)) {
                $searchTerm = strtolower($this->historySearch);
                $fullHistory = $fullHistory->filter(function ($entry) use ($searchTerm) {
                    return str_contains(strtolower($entry['vehicle_info']), $searchTerm) ||
                        str_contains(strtolower($entry['detail']), $searchTerm) ||
                        str_contains(strtolower(\Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y')), $searchTerm);
                });
            }
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

    public function showHistory($driverId)
    {
        $driver = Driver::withTrashed()->findOrFail($driverId);

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$this->canManageDriver($driver)) {
            session()->flash('error', 'Você não tem permissão para ver o histórico deste motorista.');
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
        $this->reset('historySearch'); // Resetar a busca do histórico
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            'document' => ['required', new Cpf, Rule::unique('drivers')->ignore($this->driverId)],
            'telefone' => 'nullable|string|max:20',
            'type' => 'required|in:Servidor,Aluno,Terceirizado,Visitante', // Garante que o tipo é válido
            'is_authorized' => [
                'boolean',
                // ### VALIDAÇÃO ADICIONADA AQUI ###
                // Função anônima para validação customizada
                function ($attribute, $value, $fail) {
                    // $value aqui é o valor de is_authorized (true ou false)
                    // $this->type é o valor selecionado no campo 'Tipo'
                    if ($value === true && in_array($this->type, ['Aluno', 'Visitante'])) {
                        // Se is_authorized for true E o tipo for Aluno ou Visitante, falha a validação
                        $fail('Apenas motoristas do tipo Servidor ou Terceirizado podem ser autorizados para a frota oficial.');
                    }
                },
            ],
        ];
    }

    // Adiciona a mensagem de erro correspondente se necessário (embora a função $fail já defina a mensagem)
    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.max' => 'O nome não pode ter mais de 100 caracteres.',
        'document.required' => 'O campo documento é obrigatório.',
        'document.unique' => 'Este documento já está cadastrado.',
        'type.required' => 'O campo tipo é obrigatório.',
        'type.in' => 'O tipo selecionado é inválido.',
        // A mensagem para 'is_authorized' é definida diretamente na função $fail
    ];

    public function store()
    {
        // A validação agora inclui a regra customizada
        $validatedData = $this->validate();
        $user = Auth::user();

        // Restrição adicional para Fiscal Oficial (mantida)
        if ($user->role === 'fiscal' && $user->fiscal_type === 'official' && !$validatedData['is_authorized']) {
            $this->addError('is_authorized', 'Fiscais de frota oficial só podem gerenciar motoristas autorizados.');
            return;
        }

        // Verifica permissão geral (mantida)
        $driver = $this->driverId ? Driver::withTrashed()->find($this->driverId) : null;
        if (!$this->canManageDriver($driver)) {
            abort(403, 'Ação não autorizada.');
        }

        // Usa os dados validados para criar/atualizar
        Driver::updateOrCreate(['id' => $this->driverId], [
            'name' => Str::title($validatedData['name']),
            'document' => $validatedData['document'],
            'telefone' => $validatedData['telefone'],
            'type' => $validatedData['type'],
            'is_authorized' => $validatedData['is_authorized'],
        ]);

        session()->flash('success', $this->driverId ? 'Motorista atualizado!' : 'Motorista cadastrado!');
        $this->closeModal();
    }

    public function create()
    {
        if (!in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro'])) {
            abort(403, 'Ação não autorizada.');
        }
        $this->resetInputFields();
        if (Auth::user()->role === 'fiscal' && Auth::user()->fiscal_type === 'official') {
            $this->is_authorized = true; // Fiscal oficial sempre cria autorizado
            $this->type = 'Servidor'; // Fiscal oficial só pode criar Servidor ou Terceirizado? Padrão Servidor.
        }
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);
        if (!$this->canManageDriver($driver)) {
            session()->flash('error', 'Você não tem permissão para editar este motorista.');
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
        $this->type = 'Servidor'; // Padrão
        $user = Auth::user();


        // Define is_authorized padrão com base no perfil:
        // Apenas Admin e Fiscais podem *definir* a autorização.
        // Para eles, começamos com 'true' como padrão seguro (especialmente Fiscal Oficial).
        // Para o Porteiro, o padrão deve ser 'false', pois ele não controla essa flag.
        if ($user->role === 'admin' || $user->role === 'fiscal') {
            // Fiscal oficial DEVE ser true, os outros podem começar como true por segurança.
            $this->is_authorized = ($user->role === 'fiscal' && $user->fiscal_type === 'official') ? true : true;
        } else {
            // Porteiro e outros perfis (se houver) começam com false.
            $this->is_authorized = false;
        }
        // ### FIM DA CORREÇÃO ###

        $this->resetErrorBag();
    }

    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$this->canManageDriver($driver)) {
            session()->flash('error', 'Ação não autorizada.');
            return;
        }

        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    public function deleteDriver()
    {
        $driver = Driver::find($this->driverIdToDelete);

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$driver || !$this->canManageDriver($driver)) {
            $this->closeConfirmModal();
            session()->flash('error', 'Ação não autorizada ou motorista não encontrado.');
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

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$driver || !$this->canManageDriver($driver)) {
            session()->flash('error', 'Ação não autorizada ou motorista não encontrado.');
            return;
        }

        $driver->restore();
        session()->flash('success', 'Motorista restaurado com sucesso!');
    }

    public function confirmForceDelete($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$this->canManageDriver($driver)) {
            session()->flash('error', 'Ação não autorizada.');
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
            session()->flash('error', 'Motorista não encontrado.');
            $this->closeConfirmModal();
            return;
        }

        // ### VERIFICAÇÃO DE PERMISSÃO ###
        if (!$this->canManageDriver($driver)) {
            $this->closeConfirmModal();
            session()->flash('error', 'Ação não autorizada.');
            return;
        }

        // Verificações de segurança (mantidas)
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
        session()->flash('success', 'Motorista excluído permanentemente.'); // Usando 'success' como nos outros flashes
        $this->closeConfirmModal();
    }
}
