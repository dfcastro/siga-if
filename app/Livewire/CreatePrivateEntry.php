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
            'new_visitor_document' => ['required_if:showNewVisitorForm,true', 'nullable', new Cpf, Rule::unique('drivers', 'document')],
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
            $this->drivers = Driver::where('name', 'like', '%' . $value . '%')
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
        $vehicle = Vehicle::findOrFail($vehicleId);
        $this->selectedVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate;
        $this->vehicle_model = $vehicle->model;

        if ($vehicle->driver) {
            $this->selectDriver($vehicle->driver->id, $vehicle->driver->name);
        }

        $this->search = '';
        $this->searchResults = [];
    }

    /**
     * Salva a entrada do veículo no banco de dados.
     */
    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        $this->validate();

        $driverId = $this->selected_driver_id;
        $finalReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        if ($this->showNewVisitorForm) {
            $newDriver = Driver::create([
                'name' => $this->new_visitor_name,
                'document' => preg_replace('/\D/', '', $this->new_visitor_document),
                'telefone' => $this->new_visitor_phone,
                'type' => 'Visitante',
                'is_authorized' => true,
            ]);
            $driverId = $newDriver->id;
        }

        PrivateEntry::create([
            'license_plate' => strtoupper($this->license_plate),
            'vehicle_model' => $this->vehicle_model,
            'entry_reason' => $finalReason,
            'entry_at' => now(),
            'guard_on_entry_id' => auth()->id(), // <-- Alterado para ID
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
    }

    /**
     * Renderiza o componente na tela.
     */
    public function render()
    {
        $currentVehicles = PrivateEntry::with(['vehicle.driver', 'driver'])
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
            $this->searchResults = collect();
            return;
        }

        $vehiclesFound = Vehicle::with('driver')
            ->where('type', 'Particular')
            ->where(function ($query) use ($value) {
                $query->where('license_plate', 'like', '%' . $value . '%')
                    ->orWhere('model', 'like', '%' . $value . '%');
            })
            ->get();

        $driversFound = Driver::with(['vehicles' => function ($query) {
            $query->where('type', 'Particular');
        }])
            ->where('name', 'like', '%' . $value . '%')
            ->get();

        $formattedResults = collect();

        foreach ($vehiclesFound as $vehicle) {
            $formattedResults->push([
                'id' => $vehicle->id,
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Prop.: " . ($vehicle->driver ? $vehicle->driver->name : 'Sem proprietário')
            ]);
        }

        foreach ($driversFound as $driver) {
            foreach ($driver->vehicles as $vehicle) {
                $formattedResults->push([
                    'id' => $vehicle->id,
                    'text' => "MOTORISTA: {$driver->name} - Veículo: {$vehicle->license_plate} ({$vehicle->model})"
                ]);
            }
        }

        $this->searchResults = $formattedResults->unique('id')->sortBy('text');
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
