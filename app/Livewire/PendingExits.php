<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Carbon\Carbon;

class PendingExits extends Component
{
    // Propriedades para o modal
    public bool $isConfirmModalOpen = false;
    public $itemToConfirm;
    public $confirmationMessage;
    public $actionType;

    // Prepara e abre o modal de confirmação
    public function confirmRegistration($id, $type, $action)
    {
        $this->itemToConfirm = ($type === 'private')
            ? PrivateEntry::findOrFail($id)
            : OfficialTrip::findOrFail($id);

        $this->actionType = $action;
        $this->isConfirmModalOpen = true;
    }

    // Executa a ação após a confirmação
    public function executeRegistration()
    {
        if (!$this->itemToConfirm) return;

        $now = Carbon::now();
        $user = auth()->user()->name;

        if ($this->itemToConfirm instanceof PrivateEntry && $this->actionType === 'exit') {
            $this->itemToConfirm->update(['exit_at' => $now, 'guard_on_exit' => $user]);
            session()->flash('message', 'Saída de veículo particular registrada!');
        } elseif ($this->itemToConfirm instanceof OfficialTrip && $this->actionType === 'arrival') {
            $this->itemToConfirm->update(['arrival_datetime' => $now, 'guard_on_arrival' => $user]);
            session()->flash('message', 'Chegada de veículo oficial registrada!');
        }

        $this->closeConfirmModal();
    }

    // Fecha o modal
    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
        $this->reset('itemToConfirm', 'confirmationMessage', 'actionType');
    }

    public function render()
    {
        $privateLimit = Carbon::now()->subHours(12);

        // ATUALIZAÇÃO AQUI: Adicionado 'driver' ao with()
        $pendingPrivateEntries = PrivateEntry::whereNull('exit_at')
            ->where('entry_at', '<', $privateLimit)
            ->with('vehicle', 'driver')
            ->latest('entry_at')
            ->get();

        // ATUALIZAÇÃO AQUI: Adicionado 'driver' ao with()
        $pendingOfficialTrips = OfficialTrip::whereNotNull('departure_datetime')
            ->whereNull('arrival_datetime')
            ->with('vehicle', 'driver', 'user')
            ->oldest('departure_datetime')
            ->get();

        return view('livewire.pending-exits', [
            'pendingPrivateEntries' => $pendingPrivateEntries,
            'pendingOfficialTrips' => $pendingOfficialTrips,
        ]);
    }
}
