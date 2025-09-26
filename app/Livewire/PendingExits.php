<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Carbon\Carbon;

class PendingExits extends Component
{
    public $pendingPrivateEntries;
    public $pendingOfficialTrips;

    public function mount()
    {
        $this->loadPendingExits();
    }

    // app/Livewire/PendingExits.php

    public function loadPendingExits()
    {
        // A lógica para veículos particulares
        $privateLimit = Carbon::now()->subHours(12);
        $this->pendingPrivateEntries = PrivateEntry::whereNull('exit_at')
            ->where('entry_at', '<', $privateLimit)
            ->with('vehicle.driver')
            ->latest('entry_at')
            ->get();

        //  Lógica para veículos oficiais
        // Agora buscamos TODAS as viagens sem data de chegada, ordenadas pela mais antiga.
        $this->pendingOfficialTrips = OfficialTrip::whereNull('arrival_datetime')
            ->with('vehicle.driver', 'user')
            ->oldest('departure_datetime') // Usamos 'oldest' para ver as mais antigas primeiro
            ->get();
    }
    public function registerExit($id, $type)
    {
        $now = Carbon::now();

        if ($type === 'private') {
            $entry = PrivateEntry::find($id);
            if ($entry) {
                $entry->update(['exit_at' => $now]);
            }
        } elseif ($type === 'official') {
            $trip = OfficialTrip::find($id);
            if ($trip) {
                // CORREÇÃO: Atualizar a coluna 'arrival_datetime'
                $trip->update(['arrival_datetime' => $now]);
            }
        }

        session()->flash('message', 'Saída registrada com sucesso!');

        $this->loadPendingExits();
    }

    public function render()
    {
        return view('livewire.pending-exits');
    }
}
