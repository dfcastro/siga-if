<?php

namespace App\Livewire;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends Component
{
    public $vehiclesInYard;
    public $officialTripsInProgress;
    public $selectedDate;

    // NOVO: Propriedade pública para o Livewire nunca esquecer dela
    public bool $isPorteiro = false;

    public $reportData;
    public $daysForReport = 7;

    public function mount()
    {
        // Define de uma vez por todas se é porteiro logo ao carregar a página
        $this->isPorteiro = Auth::user()->role === 'porteiro';
        
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadNonDateDependentStats();
        $this->getReportData();
    }

    #[On('stats-updated')]
    public function loadNonDateDependentStats()
    {
        // O Pátio e as Viagens em Andamento são sempre GLOBAIS.
        $this->vehiclesInYard = PrivateEntry::whereNull('exit_at')->count();
        $this->officialTripsInProgress = OfficialTrip::whereNull('arrival_datetime')->count();
    }

    public function updatedSelectedDate($value)
    {
        $selectedCarbonDate = Carbon::parse($value);

        // Query Base do Gráfico
        $chartQuery = PrivateEntry::whereDate('entry_at', $selectedCarbonDate);
        
        // Filtro Exclusivo usando a propriedade pública
        if ($this->isPorteiro) {
            $chartQuery->where('guard_on_entry_id', Auth::id());
        }

        $entriesByHour = $chartQuery->get()
            ->groupBy(fn($entry) => Carbon::parse($entry->entry_at)->format('H'))
            ->map(fn($group) => $group->count());

        $hours = collect(range(0, 23))->map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT));
        $entriesByHourData = $hours->map(fn($hour) => $entriesByHour->get($hour, 0))->values()->toArray();

        $this->dispatch('updateChartData', data: $entriesByHourData);
    }

    public function render()
    {
        $selectedCarbonDate = Carbon::parse($this->selectedDate);

        // 1. Queries de Totais do Dia
        $privateQuery = PrivateEntry::whereDate('entry_at', $selectedCarbonDate);
        $officialQuery = OfficialTrip::whereDate('departure_datetime', $selectedCarbonDate);

        // Se for Porteiro, filtra os cartões do dia para mostrar apenas as ações dele
        if ($this->isPorteiro) {
            $privateQuery->where('guard_on_entry_id', Auth::id());
            $officialQuery->where('guard_on_departure_id', Auth::id());
        }

        $totalPrivateEntriesToday = $privateQuery->count();
        $totalOfficialDeparturesToday = $officialQuery->count();

        // 2. Query do Gráfico (Para a carga inicial)
        $chartQuery = PrivateEntry::whereDate('entry_at', $selectedCarbonDate);
        if ($this->isPorteiro) {
            $chartQuery->where('guard_on_entry_id', Auth::id());
        }

        $entriesByHour = $chartQuery->get()
            ->groupBy(fn($entry) => Carbon::parse($entry->entry_at)->format('H'))
            ->map(fn($group) => $group->count());

        $hours = collect(range(0, 23))->map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT));
        $entriesByHourData = $hours->map(fn($hour) => $entriesByHour->get($hour, 0))->values()->toArray();

        // Como $isPorteiro agora é "public", não precisamos mais enviá-lo pelo array do render!
        return view('livewire.dashboard-stats', [
            'totalPrivateEntriesToday' => $totalPrivateEntriesToday,
            'totalOfficialDeparturesToday' => $totalOfficialDeparturesToday,
            'entriesByHourData' => $entriesByHourData,
        ]);
    }

    private function getReportData()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($this->daysForReport);

        $privateEntries = PrivateEntry::whereBetween('entry_at', [$startDate, $endDate])->get()->groupBy(fn($date) => Carbon::parse($date->entry_at)->format('d/m'));
        $officialTrips = OfficialTrip::whereBetween('departure_datetime', [$startDate, $endDate])->get()->groupBy(fn($date) => Carbon::parse($date->departure_datetime)->format('d/m'));

        $labels = [];
        $privateData = [];
        $officialData = [];

        for ($i = $this->daysForReport - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $label = $date->format('d/m');
            $labels[] = $label;
            $privateData[] = $privateEntries->has($label) ? $privateEntries[$label]->count() : 0;
            $officialData[] = $officialTrips->has($label) ? $officialTrips[$label]->count() : 0;
        }

        $this->reportData = [
            'labels' => $labels,
            'private_data' => $privateData,
            'official_data' => $officialData,
            'top_private_drivers' => Driver::withCount('privateEntries')->orderBy('private_entries_count', 'desc')->take(5)->get(),
            'top_official_drivers' => Driver::withCount('officialTrips')->orderBy('official_trips_count', 'desc')->take(5)->get(),
            'most_used_official_vehicles' => Vehicle::where('type', 'Oficial')->withCount('officialTrips')->orderBy('official_trips_count', 'desc')->take(5)->get(),
        ];
    }
}