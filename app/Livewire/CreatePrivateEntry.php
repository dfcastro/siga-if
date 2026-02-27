<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use App\Rules\Cpf;

#[Layout('layouts.app')]
class CreatePrivateEntry extends Component
{
    // --- PROPRIEDADES DO FORMULÁRIO PRINCIPAL ---
    public string $license_plate = '';
    public string $vehicle_model = '';
    public string $entry_reason = '';
    public string $other_reason = '';
    public string $successMessage = '';
    public string $exitSearch = '';
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null;
    public $showExitConfirmationModal = false;
    public $entryToExit = null;
    public array $predefinedReasons = [
        'Entrada de Servidor',
        'Reunião',
        'Entrega de Material',
        'Visita Técnica',
        'Evento',
        'Prestação de Serviço',
        'Pais de aluno, buscar aluno, trazer aluno,etc',
    ];

    // --- PROPRIEDADES REFINADAS E UNIFICADAS PARA O MOTORISTA ---
    public $selected_driver_id = null;
    public string $driver_search = '';
    public $drivers = [];
    public $suggestedDrivers = [];

    // --- Estado do Formulário de Novo Visitante ---
    public bool $showNewVisitorForm = false;
    public string $new_visitor_name = '';
    public string $new_visitor_document = '';
    public string $new_visitor_phone = '';

    /**
     * Regras de validação unificadas.
     */
    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            'selected_driver_id' => 'required_without:new_visitor_name',
            'new_visitor_name' => 'required_if:showNewVisitorForm,true|string|max:100',

            // ANTES ESTAVA ASSIM:
            // 'new_visitor_document' => ['required_if:showNewVisitorForm,true', 'nullable', new Cpf, Rule::unique('drivers', 'document')],

            // DEIXE ASSIM (Sem o Rule::unique):
            'new_visitor_document' => ['required_if:showNewVisitorForm,true', 'nullable', new Cpf],

            'new_visitor_phone' => 'nullable|string|max:20',
        ];
    }

    // --- ADIÇÃO PARA VALIDAÇÃO EM TEMPO REAL ---
    /**
     * Este método é executado automaticamente sempre que uma propriedade
     * com wire:model.live ou wire:model.blur é atualizada.
     * Ele valida APENAS o campo que foi alterado.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    // --- FIM DA ADIÇÃO ---

    /**
     * Acionado sempre que o campo de busca de motorista é alterado.
     */
    public function updatedDriverSearch($value)
    {
        $this->selected_driver_id = null;
        $this->showNewVisitorForm = false;

        if (strlen($value) >= 2) {
            $cleanSearch = preg_replace('/\D/', '', $value); // Pega só os números para a busca por CPF

            $this->drivers = Driver::where('name', 'like', '%' . $value . '%')
                ->when(strlen($cleanSearch) > 0, function ($query) use ($cleanSearch) {
                    $query->orWhere('document', 'like', '%' . $cleanSearch . '%');
                })
                ->orderBy('name')
                ->limit(5)
                ->get();
        } else {
            $this->drivers = [];
        }
    }
    /**
     * Seleciona um motorista existente da lista de resultados.
     */
    public function selectDriver($id, $name)
    {
        $this->selected_driver_id = $id;
        $this->driver_search = $name;
        $this->drivers = []; // Esconde a lista de resultados
    }

    /**
     * Prepara a UI para cadastrar um novo visitante.
     */
    public function prepareNewVisitorForm()
    {
        $this->showNewVisitorForm = true;
        $this->new_visitor_name = $this->driver_search;
        $this->drivers = [];
    }

    /**
     * Cancela o modo de cadastro e limpa os campos do novo visitante.
     */
    public function cancelNewVisitor()
    {
        $this->showNewVisitorForm = false;
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone');
        $this->resetErrorBag(['new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
    }

    /**
     * Preenche os dados do formulário ao selecionar um veículo na busca rápida.
     */
    public function selectVehicle($vehicleId)
    {
        // Carrega o veículo e a lista de motoristas vinculados
        $vehicle = Vehicle::with('drivers')->findOrFail($vehicleId);

        $this->selectedVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate;
        $this->vehicle_model = $vehicle->model;

        // Guarda os motoristas na propriedade para exibir os botões na tela
        $this->suggestedDrivers = $vehicle->drivers;

        // Se o veículo tiver APENAS UM motorista, já seleciona ele automaticamente
        if ($this->suggestedDrivers->count() === 1) {
            $motoristaPadrao = $this->suggestedDrivers->first();
            $this->selectDriver($motoristaPadrao->id, $motoristaPadrao->name);
        } else {
            // Se tiver mais de um (ou nenhum), deixa em branco pro porteiro escolher
            $this->selected_driver_id = null;
            $this->driver_search = '';
        }

        $this->search = '';
        $this->searchResults = [];
    }

    /**
     * Função chamada quando o porteiro clica na tag de sugestão rápida de motorista.
     */
    public function useSuggestedDriver($driverId, $driverName)
    {
        $this->selectDriver($driverId, $driverName);
    }



    /**
     * Salva a entrada do veículo no banco de dados.
     */
    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        // ### ADICIONE ESTE BLOCO AQUI ###
        // Validação manual e amigável para CPF duplicado
        if ($this->showNewVisitorForm && !empty($this->new_visitor_document)) {
            $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);
            $existingDriver = Driver::where('document', $cleanDocument)->first();

            if ($existingDriver) {
                // Se já existe, avisa quem é o dono do CPF para o porteiro
                $this->addError('new_visitor_document', "Este CPF já está cadastrado para: {$existingDriver->name}. Cancele este formulário e busque pelo nome dele.");
                return; // Interrompe o processo aqui
            }
        }
        // ### FIM DO BLOCO ADICIONADO ###

        $this->validate();

        $driverId = $this->selected_driver_id;
        $finalReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        // Cria o visitante se for o caso
        if ($this->showNewVisitorForm) {
            $newDriver = Driver::create([
                'name' => $this->new_visitor_name,
                'document' => preg_replace('/\D/', '', $this->new_visitor_document),
                'telefone' => $this->new_visitor_phone,
                'type' => 'Visitante',
                'is_authorized' => false,
            ]);
            $driverId = $newDriver->id;
        }

        // Se temos um veículo e um motorista, vinculamos os dois.
        if ($this->selectedVehicleId && $driverId) {
            $vehicle = Vehicle::find($this->selectedVehicleId);
            if ($vehicle) {
                $vehicle->drivers()->syncWithoutDetaching([$driverId]);
            }
        }

        PrivateEntry::create([
            'license_plate' => strtoupper($this->license_plate),
            'vehicle_model' => $this->vehicle_model,
            'entry_reason' => $finalReason,
            'entry_at' => now(),
            'guard_on_entry_id' => auth()->id(),
            'vehicle_id' => $this->selectedVehicleId,
            'driver_id' => $driverId,
        ]);

        $this->dispatch('stats-updated');
        $this->successMessage = 'Entrada do veículo ' . $this->license_plate . ' registrada com sucesso!';
        $this->resetForm();
    }

    /**
     * Reseta todas as propriedades públicas do componente para o estado inicial.
     */
    public function resetForm()
    {
        $this->reset();
        $this->suggestedDrivers = [];
    }

    /**
     * Renderiza o componente na tela.
     */
    public function render()
    {
        $currentVehicles = PrivateEntry::with(['vehicle.drivers', 'driver'])
            ->whereNull('exit_at')
            ->when($this->exitSearch, function ($query) {
                $searchTerm = '%' . $this->exitSearch . '%';
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('license_plate', 'like', $searchTerm)
                        ->orWhereHas('driver', function ($driverQuery) use ($searchTerm) {
                            $driverQuery->where('name', 'like', $searchTerm);
                        });
                });
            })
            ->latest('entry_at')
            ->get();

        return view('livewire.create-private-entry', [
            'currentVehicles' => $currentVehicles
        ]);
    }


    /**
     * Lógica para a busca rápida de veículos.
     */
    public function updatedSearch($value)
    {
        if (strlen($value) < 3) {
            $this->searchResults = [];
            return;
        }

        // Busca os veículos (pela placa ou modelo) e já traz a lista de motoristas
        $vehiclesFound = Vehicle::with('drivers')
            ->where('type', 'Particular')
            ->where(function ($query) use ($value) {
                $query->where('license_plate', 'like', '%' . $value . '%')
                    ->orWhere('model', 'like', '%' . $value . '%');
            })
            ->get();

        // Busca os motoristas pelo nome e traz os veículos vinculados a eles
        $cleanSearch = preg_replace('/\D/', '', $value);

        // Busca os motoristas pelo nome ou CPF e traz os veículos vinculados
        $driversFound = Driver::with(['vehicles' => function ($query) {
            $query->where('type', 'Particular');
        }])
            ->where('name', 'like', '%' . $value . '%')
            ->when(strlen($cleanSearch) > 0, function ($query) use ($cleanSearch) {
                $query->orWhere('document', 'like', '%' . $cleanSearch . '%');
            })
            ->get();
        $formattedResults = collect();

        // 1. Adiciona os resultados encontrados pela PLACA
        foreach ($vehiclesFound as $vehicle) {
            // Pega o nome de TODOS os motoristas vinculados, separados por vírgula
            $nomesProprietarios = $vehicle->drivers->count() > 0
                ? $vehicle->drivers->pluck('name')->join(', ')
                : 'Sem motorista vinculado';

            $formattedResults->push([
                'id' => $vehicle->id,
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Motoristas: {$nomesProprietarios}"
            ]);
        }

        // 2. Adiciona os resultados encontrados pelo NOME ou CPF do motorista
        foreach ($driversFound as $driver) {
            foreach ($driver->vehicles as $vehicle) {
                $formattedResults->push([
                    'id' => $vehicle->id,
                    // Olha como fica limpo chamando o formatted_document direto aqui:
                    'text' => "MOTORISTA: {$driver->name} (CPF: {$driver->formatted_document}) - Veículo: {$vehicle->license_plate} ({$vehicle->model})"
                ]);
            }
        }

        $this->searchResults = $formattedResults->unique('id')->sortBy('text')->values()->toArray();

        // A MÁGICA ESTÁ AQUI NO FINAL: ->values()->toArray()
        // Isso reseta os índices do array garantindo que o Livewire renderize todos os itens na tela!
        $this->searchResults = $formattedResults->unique('id')->sortBy('text')->values()->toArray();
    }

    /**
     * Prepara o modal de confirmação de saída.
     */
    public function confirmExit($entryId)
    {
        $this->entryToExit = PrivateEntry::with('driver')->findOrFail($entryId);
        $this->showExitConfirmationModal = true;
    }

    /**
     * Executa o registro da saída do veículo.
     */
    public function executeExit()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        if ($this->entryToExit) {
            $this->entryToExit->exit_at = now();
            $this->entryToExit->guard_on_exit_id = auth()->id(); // <-- Alterado para ID
            $this->entryToExit->save();

            $this->successMessage = 'Saída do veículo ' . ($this->entryToExit->license_plate) . ' registrada com sucesso!';
            $this->dispatch('stats-updated');
        }

        $this->showExitConfirmationModal = false;
        $this->entryToExit = null;
    }
}
