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
    // SUAS PROPRIEDADES EXISTENTES
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

    // --- NOVAS PROPRIEDADES PARA O CAMPO DE MOTORISTA ---
    public $selected_driver_id = null; // Manteve-se para o ID
    public string $driver_search = ''; // O texto visível no campo de busca
    public $driver_results = []; // Resultados da busca de motoristas
    public bool $show_driver_dropdown = false; // Controla a visibilidade do dropdown
    public bool $isNewVisitor = false; // Flag para indicar um novo motorista
    public string $visitor_document = ''; // CPF do novo motorista

    // Regras de validação atualizadas
    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            'selected_driver_id' => 'required_if:isNewVisitor,false|nullable|exists:drivers,id',
            'driver_search' => 'required_if:isNewVisitor,true|string|max:255',
            'visitor_document' => ['required_if:isNewVisitor,true', 'nullable', new Cpf, Rule::unique('drivers', 'document')],
        ];
    }

    // Validação em tempo real para o CPF
    public function updated($propertyName)
    {
        if ($propertyName === 'visitor_document') {
            $this->validateOnly($propertyName);
        }
    }

    // --- NOVOS MÉTODOS PARA O CAMPO DE MOTORISTA ---

    // Acionado sempre que o campo de busca de motorista é alterado
    public function updatedDriverSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->driver_results = Driver::where('name', 'like', '%' . $value . '%')
                ->orderBy('name')
                ->limit(5)
                ->get();
            $this->show_driver_dropdown = true;
        } else {
            $this->driver_results = [];
            $this->show_driver_dropdown = false;
        }
        $this->isNewVisitor = false;
        $this->selected_driver_id = null; // Limpa o ID se o texto mudar
    }

    // Seleciona um motorista existente da lista
    public function selectDriver($id, $name)
    {
        $this->selected_driver_id = $id;
        $this->driver_search = $name;
        $this->isNewVisitor = false;
        $this->show_driver_dropdown = false;
        $this->driver_results = [];
    }

    // Prepara para criar um novo motorista
    public function createNewDriver()
    {
        $this->isNewVisitor = true;
        $this->driver_search = trim($this->driver_search);
        $this->selected_driver_id = null;
        $this->show_driver_dropdown = false;
        $this->driver_results = [];
    }

    // --- SEUS MÉTODOS EXISTENTES (COM PEQUENOS AJUSTES) ---

    public function selectVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $this->selectedVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate;
        $this->vehicle_model = $vehicle->model;

        // Ajuste: Preenche o nosso novo campo de busca de motorista
        if ($vehicle->driver) {
            $this->selectDriver($vehicle->driver->id, $vehicle->driver->name);
        }

        $this->search = '';
        $this->searchResults = [];
    }

    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        $this->validate();

        $driverId = $this->selected_driver_id;
        $finalReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        if ($this->isNewVisitor) {
            $newDriver = Driver::create([
                'name' => $this->driver_search, // Usa o nome digitado na busca
                'document' => preg_replace('/\D/', '', $this->visitor_document),
                'type' => 'Visitante',
                'is_authorized' => false,
            ]);
            $driverId = $newDriver->id;
        }

        PrivateEntry::create([
            'license_plate' => strtoupper($this->license_plate),
            'vehicle_model' => $this->vehicle_model,
            'entry_reason' => $finalReason,
            'entry_at' => now(),
            'guard_on_entry' => auth()->user()->name,
            'vehicle_id' => $this->selectedVehicleId,
            'driver_id' => $driverId,
        ]);
        $this->dispatch('stats-updated');
        $this->successMessage = 'Entrada do veículo ' . $this->license_plate . ' registrada com sucesso!';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'license_plate',
            'vehicle_model',
            'entry_reason',
            'other_reason',
            'search',
            'searchResults',
            'selectedVehicleId',
            'selected_driver_id',
            'isNewVisitor',
            'visitor_document',
            'driver_search',
            'driver_results',
            'show_driver_dropdown'
        ]);
    }

    // O resto dos seus métodos (render, confirmExit, executeExit, etc.) permanecem iguais
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
        // Não precisamos mais passar os drivers para a view desta forma
        return view('livewire.create-private-entry', [
            'currentVehicles' => $currentVehicles
        ]);
    }

    public function updatedSearch($value)
    {
        if (strlen($value) < 3) {
            $this->searchResults = collect();
            return;
        }
        $vehiclesFound = Vehicle::with('driver')
            ->where('license_plate', 'like', '%' . $value . '%')
            ->orWhere('model', 'like', '%' . $value . '%')
            ->get();
        $driversFound = Driver::with('vehicles')
            ->where('name', 'like', '%' . $value . '%')
            ->get();
        $formattedResults = collect();
        foreach ($vehiclesFound as $vehicle) {
            $formattedResults->push([
                'id' => $vehicle->id,
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Prop.: {$vehicle->driver->name}"
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

    public function confirmExit($entryId)
    {
        $this->entryToExit = PrivateEntry::with('driver')->findOrFail($entryId);
        $this->showExitConfirmationModal = true;
    }

    public function executeExit()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        if ($this->entryToExit) {
            $this->entryToExit->exit_at = now();
            $this->entryToExit->guard_on_exit = auth()->user()->name;
            $this->entryToExit->save();

            $this->successMessage = 'Saída do veículo ' . ($this->entryToExit->license_plate) . ' registrada com sucesso!';
            $this->dispatch('stats-updated');
        }

        $this->showExitConfirmationModal = false;
        $this->entryToExit = null;
    }
}
