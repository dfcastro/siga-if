<?php

namespace App\Livewire;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Illuminate\Support\Facades\Auth; // Use Auth facade
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class PendingExits extends Component
{
    public $pendingPrivateEntries;
    public $pendingOfficialTrips;

    // Propriedades para o modal de SAÍDA (veículos particulares)
    public $itemToConfirm;
    public $actionType;
    public $isConfirmModalOpen = false;

    // Propriedades para o modal de CHEGADA (veículos oficiais)
    public ?OfficialTrip $tripToUpdate = null;
    public $arrival_odometer;
    public bool $isArrivalModalOpen = false;

    // Adiciona listener para recarregar dados quando stats-updated é emitido
    protected $listeners = ['stats-updated' => 'loadPendingData'];

    public function mount()
    {
        $this->loadPendingData();
    }

    /**
     * Carrega os registros pendentes associados ao porteiro logado.
     */
    public function loadPendingData()
    {
        $guardId = Auth::id(); // <-- USA O ID

        // Busca entradas particulares onde o porteiro logado registrou a ENTRADA
        // e a saída ainda não foi registrada (e tem mais de 12 horas)
        $this->pendingPrivateEntries = PrivateEntry::where('guard_on_entry_id', $guardId) // <-- CORRIGIDO para ID
            ->whereNull('exit_at')
            ->where('entry_at', '<', now()->subHours(12)) // Lógica de pendência mantida
            ->with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()]) // Carrega relações
            ->latest('entry_at')
            ->get();

        // Busca viagens oficiais onde o porteiro logado registrou a PARTIDA
        // e a chegada ainda não foi registrada
        $this->pendingOfficialTrips = OfficialTrip::where('guard_on_departure_id', $guardId) // <-- CORRIGIDO para ID
            ->whereNull('arrival_datetime')
            // Não precisa de filtro de tempo aqui, pois a pendência é simplesmente a falta da chegada
            ->with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()]) // Carrega relações
            ->latest('departure_datetime')
            ->get();
    }

    // --- Lógica para SAÍDA de Veículos Particulares ---

    /**
     * Abre o modal de confirmação para registrar a saída de um veículo particular.
     */
    public function confirmRegistration($id, $type, $action)
    {
        // A lógica original focava apenas em 'private'/'exit', mantida por segurança.
        if ($type === 'private' && $action === 'exit') {
            $this->itemToConfirm = PrivateEntry::with('driver', 'vehicle')->find($id);
            if ($this->itemToConfirm) { // Verifica se encontrou
                $this->actionType = $action;
                $this->isConfirmModalOpen = true;
            } else {
                session()->flash('error', 'Registro particular não encontrado.');
            }
        }
        // Poderia adicionar lógica para outros tipos/ações se necessário
    }

    /**
     * Executa o registro da SAÍDA do veículo particular.
     */
    public function executeRegistration()
    {
        if ($this->actionType === 'exit' && $this->itemToConfirm instanceof PrivateEntry) {
            $this->itemToConfirm->update([
                'exit_at' => now(),
                // 'guard_on_exit' => Auth::user()->name, // <-- REMOVIDO
                'guard_on_exit_id' => Auth::id(), // <-- CORRIGIDO para ID
            ]);
            session()->flash('message', 'Saída de veículo particular registrada com sucesso.');
            $this->dispatch('stats-updated'); // Emite evento para atualizar outros componentes (como DashboardStats)
        }

        $this->closeConfirmModal();
        $this->loadPendingData(); // Recarrega a lista de pendentes
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->itemToConfirm = null;
        $this->actionType = null;
    }

    // --- Lógica para CHEGADA de Veículos Oficiais ---

    /**
     * Abre o modal para registrar a chegada de um veículo oficial.
     */
    public function openArrivalModal($tripId)
    {
        $this->resetErrorBag(); // Limpa erros de validação anteriores
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->find($tripId); // Usar find para evitar erro se já foi concluído por outro meio

        if (!$this->tripToUpdate || $this->tripToUpdate->arrival_datetime !== null) {
            session()->flash('error', 'Esta viagem já foi finalizada ou não foi encontrada.');
            $this->tripToUpdate = null; // Garante que não prossiga
            $this->loadPendingData(); // Atualiza a lista
            return;
        }

        $this->arrival_odometer = ''; // Limpa o campo
        $this->isArrivalModalOpen = true;
    }

    /**
     * Salva o registro da CHEGADA do veículo oficial.
     */
    public function saveArrival()
    {
        if (!$this->tripToUpdate) {
            session()->flash('error', 'Nenhuma viagem selecionada para registrar chegada.');
            $this->closeArrivalModal();
            return;
        }

        // Limpeza do valor do odómetro
        if (is_string($this->arrival_odometer)) {
            $this->arrival_odometer = str_replace(['.', ','], '', $this->arrival_odometer);
        }

        // Validação do odómetro de chegada
        $this->validate([
            'arrival_odometer' => 'required|integer|gt:' . $this->tripToUpdate->departure_odometer
        ], [
            'arrival_odometer.required' => 'O campo odómetro de chegada é obrigatório.',
            'arrival_odometer.integer' => 'O odómetro de chegada deve ser um número inteiro.',
            'arrival_odometer.gt' => 'O odómetro de chegada (' . number_format((int)$this->arrival_odometer, 0, ',', '.') . ' km) deve ser maior que o de saída (' . number_format($this->tripToUpdate->departure_odometer, 0, ',', '.') . ' km).',
        ]);

        // Cálculo da distância
        $distance = $this->arrival_odometer - $this->tripToUpdate->departure_odometer;

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer,
            'arrival_datetime' => now(),
            // 'guard_on_arrival' => auth()->user()->name, // <-- REMOVIDO
            'guard_on_arrival_id' => Auth::id(), // <-- CORRIGIDO para ID
            'distance_traveled' => $distance,
        ]);

        session()->flash('message', 'Chegada de veículo oficial registrada com sucesso!');
        $this->closeArrivalModal();
        $this->loadPendingData(); // Recarrega a lista de pendentes
        $this->dispatch('stats-updated'); // Emite evento
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
        $this->reset('arrival_odometer');
        $this->resetErrorBag(['arrival_odometer']); // Limpa erro específico
    }

    public function render()
    {
        // Recarrega os dados a cada renderização para garantir atualização,
        // embora o $listeners e chamadas explícitas de loadPendingData devam ser suficientes.
        // Se houver problemas de performance, pode remover esta linha.
        $this->loadPendingData();
        return view('livewire.pending-exits');
    }
}
