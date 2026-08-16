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
<<<<<<< HEAD
    public int $successMessageVersion = 0;
=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    public string $exitSearch = '';
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null;
    public $showExitConfirmationModal = false;
    public $entryToExit = null;
    public string $observation = '';
    public array $predefinedReasons = [
        'Entrada de Servidor',
        'Transporte de Alunos (Ônibus/Vans)',
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

<<<<<<< HEAD
    // --- Feedback e estado do veículo selecionado ---
    public string $driverActionMessage = '';
    public bool $selectedVehicleInPatio = false;
    public string $selectedVehicleOpenEntryAt = '';

=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
<<<<<<< HEAD
            // O cadastro de um novo motorista tem validação própria em registerNewDriver().
            // Para liberar a entrada, o motorista precisa estar previamente confirmado/selecionado.
            'selected_driver_id' => 'required',
=======
            'selected_driver_id' => 'required_without:new_visitor_name',
            'new_visitor_name' => 'required_if:showNewVisitorForm,true|string|max:100',
            'new_visitor_document' => ['required_if:showNewVisitorForm,true', 'nullable', new Cpf],
            'new_visitor_phone' => 'nullable|string|max:20',
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
        ];
    }

    public function updated($propertyName)
    {
<<<<<<< HEAD
        // Só valida propriedades que realmente pertencem ao formulário.
        // Isso evita validações desnecessárias em estados auxiliares do componente.
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
=======
        $this->validateOnly($propertyName);
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
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
<<<<<<< HEAD
        $this->showNewVisitorForm = false;
        $this->driverActionMessage = '';
    }

    public function clearDriverSelection()
    {
        $this->selected_driver_id = null;
        $this->driver_search = '';
        $this->drivers = [];
        $this->showNewVisitorForm = false;
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone');
        $this->driverActionMessage = '';
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    }

    public function prepareNewVisitorForm()
    {
        $this->showNewVisitorForm = true;
<<<<<<< HEAD
        $this->selected_driver_id = null;
        $this->new_visitor_name = $this->driver_search;
        $this->drivers = [];
        $this->driverActionMessage = '';
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
=======
        $this->new_visitor_name = $this->driver_search;
        $this->drivers = [];
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    }

    public function cancelNewVisitor()
    {
        $this->showNewVisitorForm = false;
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone');
        $this->resetErrorBag(['new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
    }

<<<<<<< HEAD
    /**
     * Cadastra o motorista em uma ação própria.
     *
     * Se um veículo cadastrado estiver selecionado, o novo motorista já é vinculado
     * a ele imediatamente. A entrada do veículo continua sendo uma ação separada.
     */
    public function registerNewDriver()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        $this->driverActionMessage = '';

        if (!$this->showNewVisitorForm) {
            $this->driverActionMessage = 'Abra o formulário de novo motorista antes de confirmar o cadastro.';
            return;
        }

        $this->validate([
            'new_visitor_name' => 'required|string|max:100',
            'new_visitor_document' => ['required', new Cpf],
            'new_visitor_phone' => 'nullable|string|max:20',
        ]);

        $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);
        $existingDriver = Driver::where('document', $cleanDocument)->first();

        if ($existingDriver) {
            $this->addError(
                'new_visitor_document',
                "Este CPF já está cadastrado para: {$existingDriver->name}. Cancele este formulário e busque pelo nome dele."
            );
            return;
        }

        $newDriver = Driver::create([
            'name' => trim($this->new_visitor_name),
            'document' => $cleanDocument,
            'telefone' => $this->new_visitor_phone,
            'type' => 'Visitante',
            'is_authorized' => false,
        ]);

        $vehicle = $this->selectedVehicleId ? Vehicle::find($this->selectedVehicleId) : null;

        if ($vehicle) {
            $vehicle->drivers()->syncWithoutDetaching([$newDriver->id]);
            $vehicle->load(['drivers' => fn ($query) => $query->orderBy('name')]);
            $this->suggestedDrivers = $vehicle->drivers;

            $this->driverActionMessage = "Motorista {$newDriver->name} cadastrado e vinculado ao veículo {$vehicle->license_plate} com sucesso.";

            if ($this->selectedVehicleInPatio) {
                $this->driverActionMessage .= ' O veículo já está no pátio; nenhuma nova entrada foi criada.';
            }
        } else {
            $this->driverActionMessage = "Motorista {$newDriver->name} cadastrado com sucesso. O vínculo com o veículo será concluído ao liberar a entrada.";
        }

        // Mantém o novo motorista selecionado para a movimentação atual.
        $this->selected_driver_id = $newDriver->id;
        $this->driver_search = $newDriver->name;
        $this->showNewVisitorForm = false;
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone');
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone']);
    }

    /**
     * Vincula um motorista já cadastrado ao veículo quando ele já está no pátio.
     * Não cria uma segunda entrada.
     */
    public function linkSelectedDriverToVehicle()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        $this->driverActionMessage = '';

        if (!$this->selectedVehicleId) {
            $this->addError('license_plate', 'Selecione um veículo cadastrado antes de vincular o motorista.');
            return;
        }

        $this->validate([
            'selected_driver_id' => 'required',
        ]);

        $vehicle = Vehicle::find($this->selectedVehicleId);
        $driver = Driver::find($this->selected_driver_id);

        if (!$vehicle || !$driver) {
            $this->addError('selected_driver_id', 'Não foi possível localizar o motorista ou o veículo selecionado.');
            return;
        }

        $alreadyLinked = $vehicle->drivers()->where('drivers.id', $driver->id)->exists();
        $vehicle->drivers()->syncWithoutDetaching([$driver->id]);
        $vehicle->load(['drivers' => fn ($query) => $query->orderBy('name')]);
        $this->suggestedDrivers = $vehicle->drivers;

        $this->driverActionMessage = $alreadyLinked
            ? "O motorista {$driver->name} já estava vinculado ao veículo {$vehicle->license_plate}."
            : "Motorista {$driver->name} vinculado ao veículo {$vehicle->license_plate} com sucesso.";
    }

=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    public function selectVehicle($resultId)
    {
        if (is_numeric($resultId)) {
            $resultId = 'V_' . $resultId;
        }

        if (str_starts_with($resultId, 'V_')) {
            $vehicleId = str_replace('V_', '', $resultId);
<<<<<<< HEAD
            $vehicle = Vehicle::with(['drivers' => fn ($query) => $query->orderBy('name')])->find($vehicleId);

            if ($vehicle) {
                $this->driverActionMessage = '';
=======
            $vehicle = Vehicle::with('drivers')->find($vehicleId);

            if ($vehicle) {
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
                $this->selectedVehicleId = $vehicle->id;
                $this->license_plate = $vehicle->license_plate;
                $this->vehicle_model = $vehicle->model;
                $this->suggestedDrivers = $vehicle->drivers;

<<<<<<< HEAD
                $openEntry = PrivateEntry::whereNull('exit_at')
                    ->where(function ($query) use ($vehicle) {
                        $query->where('vehicle_id', $vehicle->id)
                            ->orWhere('license_plate', strtoupper($vehicle->license_plate));
                    })
                    ->latest('entry_at')
                    ->first();

                $this->selectedVehicleInPatio = (bool) $openEntry;
                $this->selectedVehicleOpenEntryAt = $openEntry?->entry_at?->format('d/m/Y H:i') ?? '';

=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
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
<<<<<<< HEAD
                $this->selectedVehicleInPatio = false;
                $this->selectedVehicleOpenEntryAt = '';
=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
            }
        }

        $this->search = '';
        $this->searchResults = [];
    }

    public function useSuggestedDriver($driverId, $driverName)
    {
        $this->selectDriver($driverId, $driverName);
    }

<<<<<<< HEAD
    private function showSuccessMessage(string $message): void
    {
        $this->successMessage = $message;
        $this->successMessageVersion++;
    }

=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
    /**
     * Salva a entrada do veículo no banco de dados e faz o Vínculo Automático
     */
    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

<<<<<<< HEAD
        // A situação pode ter mudado desde a seleção do veículo. Confere novamente
        // antes de validar/gravar para impedir entrada duplicada com uma mensagem clara.
        $normalizedPlate = strtoupper(trim($this->license_plate));

        $openEntry = PrivateEntry::whereNull('exit_at')
            ->where(function ($query) use ($normalizedPlate) {
                if ($this->selectedVehicleId) {
                    $query->where('vehicle_id', $this->selectedVehicleId);

                    if ($normalizedPlate !== '') {
                        $query->orWhere('license_plate', $normalizedPlate);
                    }
                } elseif ($normalizedPlate !== '') {
                    $query->where('license_plate', $normalizedPlate);
                }
            })
            ->latest('entry_at')
            ->first();

        if ($openEntry) {
            // Se a placa foi digitada manualmente, recupera o cadastro do veículo
            // para permitir cadastrar/vincular motorista sem criar outra entrada.
            if (!$this->selectedVehicleId) {
                $vehicle = $openEntry->vehicle_id
                    ? Vehicle::with(['drivers' => fn ($query) => $query->orderBy('name')])->find($openEntry->vehicle_id)
                    : Vehicle::with(['drivers' => fn ($query) => $query->orderBy('name')])
                        ->where('license_plate', $normalizedPlate)
                        ->first();

                if ($vehicle) {
                    $this->selectedVehicleId = $vehicle->id;
                    $this->license_plate = $vehicle->license_plate;
                    $this->vehicle_model = $vehicle->model;
                    $this->suggestedDrivers = $vehicle->drivers;
                }
            }

            $this->selectedVehicleInPatio = true;
            $this->selectedVehicleOpenEntryAt = $openEntry->entry_at?->format('d/m/Y H:i') ?? '';
            $this->driverActionMessage = 'Este veículo já está no pátio. Nenhuma nova entrada foi criada. Você pode apenas cadastrar ou vincular outro motorista ao veículo.';
            return;
        }

        // O cadastro de novo motorista é uma ação independente. Enquanto o formulário
        // estiver aberto, não permite que o botão/Enter tente liberar a entrada.
        if ($this->showNewVisitorForm) {
            $this->driverActionMessage = 'Conclua ou cancele o cadastro do novo motorista antes de liberar a entrada.';
            return;
=======
        // Validação manual de CPF duplicado (Visitante Novo)
        if ($this->showNewVisitorForm && !empty($this->new_visitor_document)) {
            $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);
            $existingDriver = Driver::where('document', $cleanDocument)->first();

            if ($existingDriver) {
                $this->addError('new_visitor_document', "Este CPF já está cadastrado para: {$existingDriver->name}. Cancele este formulário e busque pelo nome dele.");
                return;
            }
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
        }

        $this->validate();

        $driverId = $this->selected_driver_id;
        // --- LÓGICA BLINDADA DE MOTIVO + OBSERVAÇÃO ---
        $baseReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        // Só junta a observação se o motivo for de fato o Transporte de Alunos
        if ($this->entry_reason === 'Transporte de Alunos (Ônibus/Vans)' && !empty($this->observation)) {
            $finalReason = $baseReason . ' | Obs: ' . $this->observation;
        } else {
            $finalReason = $baseReason;
        }

<<<<<<< HEAD
=======
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


>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
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
<<<<<<< HEAD
        $this->showSuccessMessage('Entrada liberada com sucesso para o veículo ' . strtoupper($this->license_plate) . '.');
=======
        $this->successMessage = 'Entrada do veículo ' . strtoupper($this->license_plate) . ' registrada com sucesso!';
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
        $this->resetForm();
    }

    public function resetForm()
    {
<<<<<<< HEAD
        // Limpa apenas o formulário. As mensagens ficam preservadas para aparecerem
        // depois da operação concluída.
        $this->reset([
            'license_plate',
            'vehicle_model',
            'entry_reason',
            'other_reason',
            'exitSearch',
            'search',
            'searchResults',
            'selectedVehicleId',
            'showExitConfirmationModal',
            'entryToExit',
            'observation',
            'selected_driver_id',
            'driver_search',
            'drivers',
            'suggestedDrivers',
            'showNewVisitorForm',
            'new_visitor_name',
            'new_visitor_document',
            'new_visitor_phone',
            'selectedVehicleInPatio',
            'selectedVehicleOpenEntryAt',
        ]);
        $this->resetErrorBag();
=======
        $this->reset();
        $this->suggestedDrivers = [];
        $this->observation = ''; // <-- LIMPA A OBSERVAÇÃO
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
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
<<<<<<< HEAD
            // Guarda os identificadores antes de limpar o modal para também
            // atualizar o estado da aba "Nova Entrada" na mesma requisição.
            $exitedVehicleId = $this->entryToExit->vehicle_id;
            $exitedLicensePlate = strtoupper(trim((string) $this->entryToExit->license_plate));

=======
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
            $this->entryToExit->exit_at = now();
            $this->entryToExit->guard_on_exit_id = auth()->id();
            $this->entryToExit->save();

<<<<<<< HEAD
            $this->showSuccessMessage('Saída do veículo ' . $exitedLicensePlate . ' registrada com sucesso!');

            // Se o mesmo veículo continua selecionado na aba de entrada,
            // remove imediatamente o aviso antigo de "já está no pátio".
            $selectedPlate = strtoupper(trim((string) $this->license_plate));
            $sameVehicleById = $this->selectedVehicleId
                && $exitedVehicleId
                && (string) $this->selectedVehicleId === (string) $exitedVehicleId;
            $sameVehicleByPlate = $selectedPlate !== ''
                && $exitedLicensePlate !== ''
                && $selectedPlate === $exitedLicensePlate;

            if ($sameVehicleById || $sameVehicleByPlate) {
                $this->selectedVehicleInPatio = false;
                $this->selectedVehicleOpenEntryAt = '';

                if (str_contains($this->driverActionMessage, 'já está no pátio')) {
                    $this->driverActionMessage = '';
                }
            }

=======
            $this->successMessage = 'Saída do veículo ' . strtoupper($this->entryToExit->license_plate) . ' registrada com sucesso!';
>>>>>>> 6747cf9a527ece22b3b9e1b0b4ce6c30827846e1
            $this->dispatch('stats-updated');
        }

        $this->showExitConfirmationModal = false;
        $this->entryToExit = null;
    }
}
