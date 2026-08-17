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
    public int $successMessageVersion = 0;
    public string $exitSearch = '';
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null;
    public $showExitConfirmationModal = false;
    public $entryToExit = null;
    public string $observation = '';
    public array $predefinedReasons = [
        'Aluno',
        'Servidor',
        'Terceirizado',
        'Pais/Responsáveis - Buscar ou trazer aluno',
        'Transporte de Alunos (Ônibus/Vans)',
        'Reunião',
        'Entrega de Material',
        'Visita Técnica',
        'Evento',
        'Prestação de Serviço',
    ];
    public bool $entryReasonAutoSuggested = false;

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
    public string $new_visitor_type = '';

    // --- Reativação segura de cadastro encontrado na lixeira ---
    public ?int $pendingInactiveDriverId = null;
    public string $pendingInactiveDriverName = '';

    // --- Feedback e estado do veículo selecionado ---
    public string $driverActionMessage = '';
    public bool $selectedVehicleInPatio = false;
    public string $selectedVehicleOpenEntryAt = '';

    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            // O cadastro de um novo motorista tem validação própria em registerNewDriver().
            // Para liberar a entrada, o motorista precisa estar previamente confirmado/selecionado.
            'selected_driver_id' => 'required',
        ];
    }

    public function updated($propertyName)
    {
        // Se o porteiro alterar algum dado depois de o sistema localizar um
        // cadastro inativo, a confirmação anterior deixa de ser válida.
        if (in_array($propertyName, [
            'new_visitor_name',
            'new_visitor_document',
            'new_visitor_phone',
            'new_visitor_type',
        ], true)) {
            $this->clearPendingInactiveDriver();
        }

        // Só valida propriedades que realmente pertencem ao formulário.
        // Isso evita validações desnecessárias em estados auxiliares do componente.
        if (array_key_exists($propertyName, $this->rules())) {
            $this->validateOnly($propertyName);
        }
    }

    public function updatedDriverSearch($value)
    {
        $this->selected_driver_id = null;
        $this->showNewVisitorForm = false;
        $this->clearAutoSuggestedEntryReason();

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
        $driver = Driver::find($id);

        if (!$driver) {
            $this->addError('selected_driver_id', 'O motorista selecionado não foi encontrado.');
            return;
        }

        $this->selected_driver_id = $driver->id;
        $this->driver_search = $driver->name;
        $this->drivers = [];
        $this->showNewVisitorForm = false;
        $this->driverActionMessage = '';

        $this->suggestEntryReasonForDriver($driver);
    }

    /**
     * Sugere o motivo da entrada com base no vínculo do motorista.
     * A sugestão nunca sobrescreve uma escolha feita manualmente pelo porteiro.
     */
    private function suggestEntryReasonForDriver(Driver $driver): void
    {
        $reasonByType = [
            'Aluno' => 'Aluno',
            'Servidor' => 'Servidor',
            'Terceirizado' => 'Terceirizado',
        ];

        $suggestedReason = $reasonByType[$driver->type] ?? null;

        if (!$suggestedReason) {
            $this->clearAutoSuggestedEntryReason();
            return;
        }

        if ($this->entry_reason === '' || $this->entryReasonAutoSuggested) {
            $this->entry_reason = $suggestedReason;
            $this->other_reason = '';
            $this->observation = '';
            $this->entryReasonAutoSuggested = true;
            $this->resetErrorBag(['entry_reason', 'other_reason']);
        }
    }

    private function clearAutoSuggestedEntryReason(): void
    {
        if (!$this->entryReasonAutoSuggested) {
            return;
        }

        $this->entry_reason = '';
        $this->other_reason = '';
        $this->observation = '';
        $this->entryReasonAutoSuggested = false;
    }

    public function updatedEntryReason(): void
    {
        // Alteração vinda da interface = decisão manual do porteiro.
        $this->entryReasonAutoSuggested = false;
    }

    public function clearDriverSelection()
    {
        $this->selected_driver_id = null;
        $this->driver_search = '';
        $this->drivers = [];
        $this->showNewVisitorForm = false;
        $this->clearAutoSuggestedEntryReason();
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type');
        $this->driverActionMessage = '';
        $this->clearPendingInactiveDriver();
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type']);
    }

    public function prepareNewVisitorForm()
    {
        $this->showNewVisitorForm = true;
        $this->selected_driver_id = null;
        $this->clearAutoSuggestedEntryReason();
        $this->new_visitor_name = $this->driver_search;
        $this->new_visitor_type = '';
        $this->drivers = [];
        $this->driverActionMessage = '';
        $this->clearPendingInactiveDriver();
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type']);
    }

    public function cancelNewVisitor()
    {
        $this->showNewVisitorForm = false;
        $this->clearPendingInactiveDriver();
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type');
        $this->resetErrorBag(['new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type']);
    }

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
            'new_visitor_type' => ['required', Rule::in(['Visitante', 'Aluno', 'Servidor', 'Terceirizado'])],
        ]);

        $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);

        // Consulta também cadastros excluídos logicamente. O CPF continua existindo
        // fisicamente na tabela e pode possuir índice UNIQUE; ignorar um registro
        // soft-deleted faria o INSERT seguinte estourar como erro 500.
        $existingDriver = Driver::withTrashed()
            ->where('document', $cleanDocument)
            ->first();

        if ($existingDriver) {
            // Cadastro ativo: não altera nome, perfil, telefone ou autorização.
            // Apenas reaproveita o motorista existente no fluxo atual.
            if ($existingDriver->deleted_at === null) {
                $this->useExistingDriver($existingDriver);
                return;
            }

            // Porteiro não pode reativar motorista protegido pela autorização
            // da frota oficial. A regra é a mesma de DriverManagement::canManageDriver().
            if ((bool) $existingDriver->is_authorized) {
                $this->clearPendingInactiveDriver();
                $this->addError(
                    'new_visitor_document',
                    "Este CPF pertence ao motorista {$existingDriver->name}, que está inativo e possui autorização para conduzir a frota oficial. "
                    . 'A reativação deve ser feita por um usuário responsável pela frota.'
                );
                return;
            }

            // Cadastro inativo comum: não restaura automaticamente. O porteiro
            // precisa confirmar explicitamente a reativação na própria tela.
            $this->pendingInactiveDriverId = $existingDriver->id;
            $this->pendingInactiveDriverName = trim($existingDriver->name);
            $this->resetErrorBag('new_visitor_document');
            return;
        }

        try {
            $newDriver = Driver::create([
                'name' => trim($this->new_visitor_name),
                'document' => $cleanDocument,
                'telefone' => $this->new_visitor_phone,
                'type' => $this->new_visitor_type,
                'is_authorized' => false,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Proteção adicional para concorrência ou inconsistência de dados: nunca
            // deixa uma colisão de CPF virar uma página 500 para o porteiro.
            report($e);

            if (str_contains(strtolower($e->getMessage()), 'duplicate')
                || in_array((string) $e->getCode(), ['23000', '23505'], true)) {
                $this->addError(
                    'new_visitor_document',
                    'Não foi possível cadastrar: este CPF já possui um cadastro no sistema. '
                    . 'Procure o motorista pelo nome ou restaure o cadastro se ele estiver inativo.'
                );
                return;
            }

            $this->driverActionMessage = 'Não foi possível cadastrar o motorista. O erro foi registrado para análise.';
            return;
        }

        $vehicle = $this->selectedVehicleId ? Vehicle::find($this->selectedVehicleId) : null;

        if ($vehicle) {
            $vehicle->drivers()->syncWithoutDetaching([$newDriver->id]);
            $vehicle->load(['drivers' => fn ($query) => $query->orderBy('name')]);
            $this->suggestedDrivers = $vehicle->drivers;

            $successText = "Motorista {$newDriver->name} cadastrado e vinculado ao veículo {$vehicle->license_plate} com sucesso.";

            if ($this->selectedVehicleInPatio) {
                $successText .= ' O veículo já está no pátio; nenhuma nova entrada foi criada.';
            }
        } else {
            $successText = "Motorista {$newDriver->name} cadastrado com sucesso. O vínculo com o veículo será concluído ao liberar a entrada.";
        }

        // O cadastro é concluído sem interromper o fluxo da portaria: o novo
        // motorista continua selecionado e o feedback aparece no toast global.
        $this->driverActionMessage = '';
        $this->showSuccessMessage($successText);

        // Mantém o novo motorista selecionado para a movimentação atual.
        $this->selected_driver_id = $newDriver->id;
        $this->driver_search = $newDriver->name;
        $this->showNewVisitorForm = false;
        $this->suggestEntryReasonForDriver($newDriver);
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type');
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type']);
    }

    /**
     * Reativa um motorista da lixeira somente quando ele não possui autorização
     * para conduzir a frota oficial. O porteiro pode atualizar os dados comuns,
     * mas a autorização de frota nunca é modificada por este fluxo.
     */
    public function reactivateInactiveDriver(): void
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

        if (!$this->pendingInactiveDriverId) {
            $this->addError('new_visitor_document', 'Nenhum cadastro inativo está aguardando reativação.');
            return;
        }

        $this->validate([
            'new_visitor_name' => 'required|string|max:100',
            'new_visitor_document' => ['required', new Cpf],
            'new_visitor_phone' => 'nullable|string|max:20',
            'new_visitor_type' => ['required', Rule::in(['Visitante', 'Aluno', 'Servidor', 'Terceirizado'])],
        ]);

        $cleanDocument = preg_replace('/\D/', '', $this->new_visitor_document);
        $driver = Driver::withTrashed()->find($this->pendingInactiveDriverId);

        if (!$driver || $driver->deleted_at === null || (string) $driver->document !== $cleanDocument) {
            $this->clearPendingInactiveDriver();
            $this->addError(
                'new_visitor_document',
                'O cadastro encontrado mudou desde a última consulta. Pesquise o CPF novamente antes de continuar.'
            );
            return;
        }

        if ((bool) $driver->is_authorized) {
            $this->clearPendingInactiveDriver();
            $this->addError(
                'new_visitor_document',
                "O motorista {$driver->name} possui autorização para conduzir a frota oficial. A reativação deve ser feita por um usuário responsável pela frota."
            );
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($driver) {
            $driver->restore();
            $driver->update([
                'name' => trim($this->new_visitor_name),
                'telefone' => $this->new_visitor_phone,
                'type' => $this->new_visitor_type,
                // Não alteramos is_authorized aqui por segurança.
            ]);
        });

        $driver->refresh();
        $this->finishDriverSelectionAfterRegistration(
            $driver,
            "Motorista {$driver->name} reativado com sucesso."
        );
    }

    public function cancelInactiveDriverReactivation(): void
    {
        $this->clearPendingInactiveDriver();
    }

    /**
     * Usa um CPF já ativo sem alterar os dados administrativos do motorista.
     */
    private function useExistingDriver(Driver $driver): void
    {
        $this->finishDriverSelectionAfterRegistration(
            $driver,
            "O CPF informado já estava cadastrado para {$driver->name}. O motorista existente foi selecionado."
        );
    }

    /**
     * Finaliza o fluxo de cadastro/reativação mantendo o porteiro na mesma tela.
     */
    private function finishDriverSelectionAfterRegistration(Driver $driver, string $message): void
    {
        $vehicle = $this->selectedVehicleId ? Vehicle::find($this->selectedVehicleId) : null;

        if ($vehicle) {
            $alreadyLinked = $vehicle->drivers()->where('drivers.id', $driver->id)->exists();
            $vehicle->drivers()->syncWithoutDetaching([$driver->id]);
            $vehicle->load(['drivers' => fn ($query) => $query->orderBy('name')]);
            $this->suggestedDrivers = $vehicle->drivers;

            if (!$alreadyLinked) {
                $message .= " Vinculado ao veículo {$vehicle->license_plate}.";
            }

            if ($this->selectedVehicleInPatio) {
                $message .= ' O veículo já está no pátio; nenhuma nova entrada foi criada.';
            }
        }

        $this->selected_driver_id = $driver->id;
        $this->driver_search = $driver->name;
        $this->showNewVisitorForm = false;
        $this->driverActionMessage = '';
        $this->suggestEntryReasonForDriver($driver);
        $this->showSuccessMessage($message);
        $this->clearPendingInactiveDriver();
        $this->reset('new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type');
        $this->resetErrorBag(['selected_driver_id', 'new_visitor_name', 'new_visitor_document', 'new_visitor_phone', 'new_visitor_type']);
    }

    private function clearPendingInactiveDriver(): void
    {
        $this->pendingInactiveDriverId = null;
        $this->pendingInactiveDriverName = '';
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

    public function selectVehicle($resultId)
    {
        if (is_numeric($resultId)) {
            $resultId = 'V_' . $resultId;
        }

        if (str_starts_with($resultId, 'V_')) {
            $vehicleId = str_replace('V_', '', $resultId);
            $vehicle = Vehicle::with(['drivers' => fn ($query) => $query->orderBy('name')])->find($vehicleId);

            if ($vehicle) {
                $this->driverActionMessage = '';
                $this->selectedVehicleId = $vehicle->id;
                $this->license_plate = $vehicle->license_plate;
                $this->vehicle_model = $vehicle->model;
                $this->suggestedDrivers = $vehicle->drivers;

                $openEntry = PrivateEntry::whereNull('exit_at')
                    ->where(function ($query) use ($vehicle) {
                        $query->where('vehicle_id', $vehicle->id)
                            ->orWhere('license_plate', strtoupper($vehicle->license_plate));
                    })
                    ->latest('entry_at')
                    ->first();

                $this->selectedVehicleInPatio = (bool) $openEntry;
                $this->selectedVehicleOpenEntryAt = $openEntry?->entry_at?->format('d/m/Y H:i') ?? '';

                if ($this->suggestedDrivers->count() === 1) {
                    $motoristaPadrao = $this->suggestedDrivers->first();
                    $this->selectDriver($motoristaPadrao->id, $motoristaPadrao->name);
                } else {
                    $this->selected_driver_id = null;
                    $this->driver_search = '';
                    $this->clearAutoSuggestedEntryReason();
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
                $this->selectedVehicleInPatio = false;
                $this->selectedVehicleOpenEntryAt = '';
            }
        }

        $this->search = '';
        $this->searchResults = [];
    }

    public function useSuggestedDriver($driverId, $driverName)
    {
        $this->selectDriver($driverId, $driverName);
    }

    private function showSuccessMessage(string $message): void
    {
        $this->successMessage = $message;
        $this->successMessageVersion++;
    }

    /**
     * Salva a entrada do veículo no banco de dados e faz o Vínculo Automático
     */
    public function save()
    {
        if (auth()->user()->role === 'fiscal') {
            abort(403, 'Você não tem permissão para executar esta ação.');
        }

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
        $this->showSuccessMessage('Entrada liberada com sucesso para o veículo ' . strtoupper($this->license_plate) . '.');
        $this->resetForm();
    }

    public function resetForm()
    {
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
            'new_visitor_type',
            'pendingInactiveDriverId',
            'pendingInactiveDriverName',
            'entryReasonAutoSuggested',
            'selectedVehicleInPatio',
            'selectedVehicleOpenEntryAt',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $currentVehicles = PrivateEntry::with(['vehicle.drivers', 'driver'])
            ->whereNull('exit_at')
            ->when($this->exitSearch, function ($query) {
                $searchTerm = '%' . $this->exitSearch . '%';
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('license_plate', 'like', $searchTerm)
                        ->orWhere('vehicle_model', 'like', $searchTerm)
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($searchTerm) {
                            $vehicleQuery->where('license_plate', 'like', $searchTerm)
                                ->orWhere('model', 'like', $searchTerm);
                        })
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
            // Guarda os identificadores antes de limpar o modal para também
            // atualizar o estado da aba "Nova Entrada" na mesma requisição.
            $exitedVehicleId = $this->entryToExit->vehicle_id;
            $exitedLicensePlate = strtoupper(trim((string) $this->entryToExit->license_plate));

            $this->entryToExit->exit_at = now();
            $this->entryToExit->guard_on_exit_id = auth()->id();
            $this->entryToExit->save();

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

            $this->dispatch('stats-updated');
        }

        $this->showExitConfirmationModal = false;
        $this->entryToExit = null;
    }
}
