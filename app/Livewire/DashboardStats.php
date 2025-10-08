<?php

namespace App\Livewire;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;

class DashboardStats extends Component
{
    // Propriedades que NÃO dependem da data selecionada
    public $vehiclesInYard;
    public $officialTripsInProgress;

    // Propriedade que DEPENDEM da data selecionada
    public $selectedDate;

    // Propriedades para as estatísticas dos relatórios
    public $reportData;
    public $daysForReport = 7;

    /**
     * Executado uma vez, quando o componente é criado.
     */
    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadNonDateDependentStats();
        $this->getReportData();
    }

    /**
     * Carrega/recarrega os cards que não dependem da data.
     */
    #[On('stats-updated')]
    public function loadNonDateDependentStats()
    {
        $this->vehiclesInYard = PrivateEntry::whereNull('exit_at')->count();
        $this->officialTripsInProgress = OfficialTrip::whereNull('arrival_datetime')->count();
    }

    /**
     * NOVO MÉTODO: Lifecycle Hook do Livewire.
     * Este método é executado AUTOMATICAMENTE sempre que a propriedade
     * pública '$selectedDate' for atualizada pelo front-end.
     * É o lugar perfeito para enviar os novos dados para o gráfico!
     */
    public function updatedSelectedDate($value)
    {
        // 1. Calcula os novos dados do gráfico com base na nova data ($value)
        $selectedCarbonDate = Carbon::parse($value);

        $entriesByHour = PrivateEntry::whereDate('entry_at', $selectedCarbonDate)
            ->get()
            ->groupBy(fn($entry) => Carbon::parse($entry->entry_at)->format('H'))
            ->map(fn($group) => $group->count());

        $hours = collect(range(0, 23))->map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT));
        $entriesByHourData = $hours->map(fn($hour) => $entriesByHour->get($hour, 0))->values()->toArray();

        // 2. Dispara o evento APENAS com os novos dados para o JavaScript atualizar o gráfico.
        $this->dispatch('updateChartData', data: $entriesByHourData);
    }


    /**
     * O método render agora fica mais limpo. Ele apenas prepara os dados
     * que a view precisa para ser renderizada (seja na primeira vez ou numa atualização).
     */
    public function render()
    {
        $selectedCarbonDate = Carbon::parse($this->selectedDate);

        // 1. Calcula o total de entradas para o card (ainda necessário aqui)
        $totalEntriesToday = PrivateEntry::whereDate('entry_at', $selectedCarbonDate)->count()
            + OfficialTrip::whereDate('departure_datetime', $selectedCarbonDate)->count();

        // 2. Calcula os dados do gráfico para a CARGA INICIAL da página.
        // As atualizações serão tratadas pelo método 'updatedSelectedDate'.
        $entriesByHour = PrivateEntry::whereDate('entry_at', $selectedCarbonDate)
            ->get()
            ->groupBy(fn($entry) => Carbon::parse($entry->entry_at)->format('H'))
            ->map(fn($group) => $group->count());

        $hours = collect(range(0, 23))->map(fn($hour) => str_pad($hour, 2, '0', STR_PAD_LEFT));
        $entriesByHourData = $hours->map(fn($hour) => $entriesByHour->get($hour, 0))->values()->toArray();

        // 3. Envia os dados para a view.
        // Note que não disparamos mais o evento daqui.
        return view('livewire.dashboard-stats', [
            'totalEntriesToday' => $totalEntriesToday,
            'entriesByHourData' => $entriesByHourData, // Usado para a carga inicial do gráfico
        ]);
    }

    /**
     * O método getReportData permanece o mesmo.
     */
    private function getReportData()
    {
        // ... seu método getReportData continua aqui, sem alterações ...
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
