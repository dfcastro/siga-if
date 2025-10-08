<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PersonalReport extends Component
{
    use WithPagination;

    public $reportType = 'particular';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($property)
    {
        if (in_array($property, ['reportType', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $porteiroName = Auth::user()->name;
        $results = null;

        if ($this->reportType === 'particular') {
            $query = PrivateEntry::with(['vehicle', 'driver'])
                ->where('guard_on_entry', $porteiroName) // Filtro de segurança
                ->whereBetween('entry_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);

            $results = $query->orderBy('entry_at', 'desc')->paginate(15);
        } elseif ($this->reportType === 'oficial') {
            $query = OfficialTrip::with(['vehicle', 'driver'])
                ->where('guard_on_departure', $porteiroName) // Filtro de segurança
                ->whereBetween('departure_datetime', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);

            $results = $query->orderBy('departure_datetime', 'desc')->paginate(15);
        }

       
        // Define o título dinamicamente com base no tipo de relatório
        $title = $this->reportType === 'particular'
            ? 'Relatório  - Veículos Particulares'
            : 'Relatório  - Frota Oficial';

        // Cria a string do período
        $period = 'de ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' a ' . Carbon::parse($this->endDate)->format('d/m/Y');

        return view('livewire.personal-report', [
            'results' => $results,
            'porteiroName' => $porteiroName,
            'period' => $period,
            'title' => $title, // <-- Envia a variável $title para a view
        ])->layoutData(['header' => 'Meu asdads']);
    }
}
