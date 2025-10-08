<?php

namespace App\Livewire;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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
    public $arrival_km;
    public bool $isArrivalModalOpen = false;

    public function mount()
    {
        $this->loadPendingData();
    }

    public function loadPendingData()
    {
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

    // --- Lógica para SAÍDA de Veículos Particulares ---

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

            // --- CORREÇÃO APLICADA AQUI ---
            // Dispara o evento para atualizar os cartões de estatísticas no dashboard.
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

    // --- Lógica para CHEGADA de Veículos Oficiais ---

    public function openArrivalModal($tripId)
    {
        $this->resetErrorBag();
        $this->tripToUpdate = OfficialTrip::with(['vehicle', 'driver'])->findOrFail($tripId);
        $this->arrival_km = $this->tripToUpdate->departure_odometer;
        $this->isArrivalModalOpen = true;
    }

    public function saveArrival()
    {
        if (is_string($this->arrival_km)) {
            $this->arrival_km = str_replace('.', '', $this->arrival_km);
        }

        $this->validate([
            'arrival_km' => 'required|integer|min:' . $this->tripToUpdate->departure_odometer
        ], [
            'arrival_km.min' => 'A quilometragem de chegada não pode ser menor que a de saída.'
        ]);

        $this->tripToUpdate->update([
            'arrival_km' => $this->arrival_km,
            'arrival_datetime' => now(),
            'guard_on_arrival' => auth()->user()->name,
            'is_finished' => true,
        ]);

        session()->flash('message', 'Chegada de veículo oficial registrada com sucesso.');
        $this->closeArrivalModal();
        $this->loadPendingData();
        $this->dispatch('stats-updated'); // Esta linha já estava correta
    }

    public function closeArrivalModal()
    {
        $this->isArrivalModalOpen = false;
        $this->tripToUpdate = null;
    }

    public function render()
    {
        return view('livewire.pending-exits');
    }
}
