<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use App\Rules\Cpf;
use Illuminate\Support\Str; // <-- Importante para o Str::upper funcionar

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

    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            'selected_driver_id' => 'required_without:new_visitor_name',
            'new_visitor_name' => 'required_if:showNewVisitorForm,true|string|max:100',
            'new_visitor_document' => ['required_if:showNewVisitorForm,true', 'nullable', new Cpf],
            'new_visitor_phone' => 'nullable|string|max:20',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedDriverSearch($value)
    {
        $this->selected_driver_id = null;
        $this->showNewVisitorForm = false;

        if (strlen($value) >= 2) {
            $cleanSearch = preg_replace('/\D/', '', $value);

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

    public function selectDriver($id, $name)
    {
        $this->selected_driver_id = $id;
        $this->driver_search = $name;
        $this->drivers = []; 
    }

    public function prepareNewVisitorForm()
    {
        $this->showNewVisitorForm = true;
        $this->new_visitor_name = $this->driver_search;
        $this->drivers = [];
    }

    public function cancelNewVisitor()
    {
        $this->showNewVisitorForm = false;
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone');
        $this->resetErrorBag(['new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
    }

    public function selectVehicle($resultId)
    {
        if (is_numeric($resultId)) {
            $resultId = 'V_' . $resultId;
        }

        if (str_starts_with($resultId, 'V_')) {
            $vehicleId = str_replace('V_', '', $resultId);
            $vehicle = Vehicle::with('drivers')->find($vehicleId);

            if ($vehicle) {
                $this->selectedVehicleId = $vehicle->id;
                $this->license_plate = $vehicle->license_plate;
                $this->vehicle_model = $vehicle->model;
                $this->suggestedDrivers = $vehicle->drivers;

                if ($this->suggestedDrivers->count() === 1) {
                    $motoristaPadrao = $this->suggestedDrivers->first();
                    $this->selectDriver($motoristaPadrao->id, $motoristaPadrao->name);
                } else {
                    $this->selected_driver_id = null;
                    $this->driver_search = '';
                }
            }
        } elseif (str_starts_with($resultId, 'D_')) {
            $driverId = str_replace('D_', '', $resultId);
            $driver = Driver::find($driverId);

            if ($driver) {
                $this->selectDriver($driver->id, $driver->name);
                $this->selectedVehicleId = null;
                $this->license_plate = '';
                $this->vehicle_model = '';
                $this->suggestedDrivers = collect();
            }
        }

        $this->search = '';
        $this->searchResults = [];
    }

    public function useSuggestedDriver($driverId, $driverName)
    {
        $this->selectDriver($driverId, $driverName);
    }

    /**
     * Salva a entrada do veículo no banco de dados e faz o Vínculo Automático
     */
    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        // Validação manual de CPF duplicado (Visitante Novo)
        if ($this->showNewVisitorForm && !empty($this->new_visitor_document)) {
            $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);
            $existingDriver = Driver::where('document', $cleanDocument)->first();

            if ($existingDriver) {
                $this->addError('new_visitor_document', "Este CPF já está cadastrado para: {$existingDriver->name}. Cancele este formulário e busque pelo nome dele.");
                return; 
            }
        }

        $this->validate();

        $driverId = $this->selected_driver_id;
        $finalReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        // 1. Cria o visitante se for o caso
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

        // =========================================================================
        // MÁGICA DO VÍNCULO AUTOMÁTICO
        // =========================================================================
        
        // Se o porteiro digitou uma placa que não estava selecionada na busca...
        if (!$this->selectedVehicleId && !empty($this->license_plate)) {
            // Procura o carro. Se não existir, cadastra na hora!
            $vehicle = Vehicle::firstOrCreate(
                ['license_plate' => strtoupper($this->license_plate)],
                [
                    'model' => Str::upper($this->vehicle_model),
                    'color' => 'N/I', // Não informado
                    'type'  => 'Particular',
                ]
            );
            $this->selectedVehicleId = $vehicle->id;
        }

        // Se temos o Veículo e o Motorista confirmados, fazemos a ligação deles!
        if ($this->selectedVehicleId && $driverId) {
            $vehicle = Vehicle::find($this->selectedVehicleId);
            if ($vehicle) {
                // Sincroniza sem apagar os donos anteriores desse carro
                $vehicle->drivers()->syncWithoutDetaching([$driverId]);
            }
        }
        // =========================================================================

        // 2. Grava a Entrada
        PrivateEntry::create([
            'license_plate' => strtoupper($this->license_plate),
            'vehicle_model' => Str::upper($this->vehicle_model),
            'entry_reason' => $finalReason,
            'entry_at' => now(),
            'guard_on_entry_id' => auth()->id(),
            'vehicle_id' => $this->selectedVehicleId,
            'driver_id' => $driverId,
        ]);

        $this->dispatch('stats-updated');
        $this->successMessage = 'Entrada do veículo ' . strtoupper($this->license_plate) . ' registrada com sucesso!';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset();
        $this->suggestedDrivers = [];
    }

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

    public function updatedSearch($value)
    {
        if (strlen($value) < 3) {
            $this->searchResults = [];
            return;
        }

        $vehiclesFound = Vehicle::with('drivers')
            ->where('type', 'Particular')
            ->where(function ($query) use ($value) {
                $query->where('license_plate', 'like', '%' . $value . '%')
                    ->orWhere('model', 'like', '%' . $value . '%');
            })
            ->get();

        $cleanSearch = preg_replace('/\D/', '', $value);
        $driversFound = Driver::with(['vehicles' => function ($query) {
            $query->where('type', 'Particular');
        }])
            ->where('name', 'like', '%' . $value . '%')
            ->when(strlen($cleanSearch) > 0, function ($query) use ($cleanSearch) {
                $query->orWhere('document', 'like', '%' . $cleanSearch . '%');
            })
            ->get();

        $formattedResults = collect();

        foreach ($vehiclesFound as $vehicle) {
            $nomesProprietarios = $vehicle->drivers->count() > 0
                ? $vehicle->drivers->pluck('name')->join(', ')
                : 'Sem motorista vinculado';

            $formattedResults->push([
                'id' => 'V_' . $vehicle->id, 
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Motoristas: {$nomesProprietarios}"
            ]);
        }

        foreach ($driversFound as $driver) {
            if ($driver->vehicles->count() > 0) {
                foreach ($driver->vehicles as $vehicle) {
                    $formattedResults->push([
                        'id' => 'V_' . $vehicle->id,
                        'text' => "MOTORISTA: {$driver->name} (CPF: {$driver->formatted_document}) - Veículo: {$vehicle->license_plate} ({$vehicle->model})"
                    ]);
                }
            } else {
                $formattedResults->push([
                    'id' => 'D_' . $driver->id, 
                    'text' => "MOTORISTA: {$driver->name} (CPF: {$driver->formatted_document}) - Nenhum veículo vinculado"
                ]);
            }
        }

        $this->searchResults = $formattedResults->unique('id')->sortBy('text')->values()->toArray();
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
            $this->entryToExit->guard_on_exit_id = auth()->id(); 
            $this->entryToExit->save();

            $this->successMessage = 'Saída do veículo ' . strtoupper($this->entryToExit->license_plate) . ' registrada com sucesso!';
            $this->dispatch('stats-updated');
        }

        $this->showExitConfirmationModal = false;
        $this->entryToExit = null;
    }
}