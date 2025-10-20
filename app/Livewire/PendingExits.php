<?php

namespace App\Livewire;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\ValidationException; // Importar para erros customizados

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
    public $arrival_odometer; // NOME CORRIGIDO para corresponder à base de dados
    public bool $isArrivalModalOpen = false;

    public function mount()
    {
        $this->loadPendingData();
    }

    public function loadPendingData()
    {
        // A sua lógica para carregar dados pendentes está ótima, sem necessidade de alteração.
        $guardName = Auth::user()->name;

        $this->pendingPrivateEntries = PrivateEntry::where('guard_on_entry', $guardName)
            ->whereNull('exit_at')
            ->where('entry_at', '<', now()->subHours(12))
            ->with(['vehicle', 'driver'])
            ->latest('entry_at')
            ->get();

        $this->pendingOfficialTrips = OfficialTrip::where('guard_on_departure', $guardName)
            ->whereNull('arrival_datetime')
            ->with(['vehicle', 'driver'])
            ->latest('departure_datetime')
            ->get();
    }

    // --- Lógica para SAÍDA de Veículos Particulares (sem alterações) ---

    public function confirmRegistration($id, $type, $action)
    {
        if ($type === 'private' && $action === 'exit') {
            $this->itemToConfirm = PrivateEntry::with('driver', 'vehicle')->find($id);
            $this->actionType = $action;
            $this->isConfirmModalOpen = true;
        }
    }

    public function executeRegistration()
    {
        if ($this->actionType === 'exit' && $this->itemToConfirm instanceof PrivateEntry) {
            $this->itemToConfirm->update([
                'exit_at' => now(),
                'guard_on_exit' => Auth::user()->name,
            ]);
            session()->flash('message', 'Saída de veículo particular registrada com sucesso.');
            $this->dispatch('stats-updated');
        }

        $this->closeConfirmModal();
        $this->loadPendingData();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->itemToConfirm = null;
        $this->actionType = null;
    }

    // --- Lógica para CHEGADA de Veículos Oficiais (ATUALIZADA) ---

    public function openArrivalModal($tripId)
    {
        $this->resetErrorBag();
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        $this->arrival_odometer = ''; // Limpa o campo para forçar a inserção manual
        $this->isArrivalModalOpen = true;
    }

    public function saveArrival()
    {
        // Limpeza do valor do odómetro
        if (is_string($this->arrival_odometer)) {
            $this->arrival_odometer = str_replace(['.', ','], '', $this->arrival_odometer);
        }

        // ### VALIDAÇÃO PRINCIPAL DA CHEGADA ###
        // Garante que o odómetro de chegada é um número e estritamente MAIOR que o de saída.
        $this->validate([
            'arrival_odometer' => 'required|integer|gt:' . $this->tripToUpdate->departure_odometer
        ], [
            'arrival_odometer.gt' => 'A quilometragem de chegada deve ser maior que a de saída (' . number_format($this->tripToUpdate->departure_odometer, 0, ',', '.') . ' km).',
        ]);

        // ### CÁLCULO AUTOMÁTICO DA DISTÂNCIA ###
        $distance = $this->arrival_odometer - $this->tripToUpdate->departure_odometer;

        $this->tripToUpdate->update([
            'arrival_odometer' => $this->arrival_odometer, // NOME CORRETO da coluna
            'arrival_datetime' => now(),
            'guard_on_arrival' => auth()->user()->name,
            'distance_traveled' => $distance, // Guarda a distância calculada
            // 'is_finished' => true, // Este campo parece não existir no seu model, removido para evitar erros.
        ]);

        session()->flash('message', 'Chegada de veículo oficial registrada com sucesso!');
        $this->closeArrivalModal();
        $this->loadPendingData();
        $this->dispatch('stats-updated');
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
        $this->reset('arrival_odometer');
    }

    public function render()
    {
        return view('livewire.pending-exits');
    }
}