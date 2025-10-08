<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Reports extends Component
{
    use WithPagination;

    // Propriedades para os Filtros
    public $reportType = 'oficial';
    public $startDate;
    public $endDate;
    public $selectedVehicle = '';
    public $selectedDriver = '';

    // As listas de veículos e motoristas agora são propriedades computadas, não mais carregadas no mount()

    // Propriedades para o Dashboard de Analytics
    public $totalEntriesToday;
    public $privateVehiclesIn;
    public $officialVehiclesOnTrip;
    public $entriesByHourData = [];

    public function layoutData()
    {
        return ['header' => 'Relatórios e Análises'];
    }

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    // MÉTODO RESTAURADO: Chamado pelo botão "Filtrar"
    public function generateReport()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);
        $this->resetPage();
    }

    // Hook para resetar a página e os filtros dependentes
    public function updatedReportType()
    {
        $this->reset('selectedVehicle', 'selectedDriver');
        $this->resetPage();
    }

    public function updating($property)
    {
        // Reseta a paginação para qualquer filtro que mude
        if (in_array($property, ['startDate', 'endDate', 'selectedVehicle', 'selectedDriver'])) {
            $this->resetPage();
        }
    }

    // PROPRIEDADE COMPUTADA: Filtra os veículos dinamicamente
    public function getVehiclesProperty()
    {
        if ($this->reportType === 'oficial') {
            return Vehicle::where('type', 'Oficial')->orderBy('model')->get();
        }

        // Para relatórios particulares, mostramos todos os veículos
        return Vehicle::orderBy('model')->get();
    }

    // PROPRIEDADE COMPUTADA: Carrega todos os motoristas
    public function getDriversProperty()
    {
        $query = Driver::query();

        // Se o relatório for de 'Viagens Oficiais', mostra apenas motoristas autorizados.
        if ($this->reportType === 'oficial') {
            $query->where('is_authorized', true);
        }

        return $query->orderBy('name')->get();
    }


    public function render()
    {
        // --- LÓGICA DO DASHBOARD DE ANALYTICS ---
        $today = Carbon::today();
        $this->totalEntriesToday = PrivateEntry::whereDate('entry_at', $today)->count() + OfficialTrip::whereDate('departure_datetime', $today)->count();
        $this->privateVehiclesIn = PrivateEntry::whereNull('exit_at')->count();
        $this->officialVehiclesOnTrip = OfficialTrip::whereNotNull('departure_datetime')->whereNull('arrival_datetime')->count();

        $entries = PrivateEntry::where('entry_at', '>=', Carbon::now()->subDay())
            ->select(DB::raw('HOUR(entry_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->all();

        $labels = [];
        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . 'h';
            $data[] = $entries[$i] ?? 0;
        }
        $this->entriesByHourData = ['labels' => $labels, 'data' => $data];
        $this->dispatch('reportDataUpdated', data: $this->entriesByHourData);

        // --- LÓGICA DA TABELA DE RELATÓRIOS DETALHADOS ---
        if ($this->reportType === 'oficial') {
            $query = OfficialTrip::with(['vehicle', 'driver'])
                ->whereNotNull('arrival_datetime')
                ->whereBetween('departure_datetime', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);
        } else {
            $query = PrivateEntry::with(['vehicle', 'driver'])
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);
        }

        if ($this->selectedVehicle) {
            $query->where('vehicle_id', $this->selectedVehicle);
        }
        if ($this->selectedDriver) {
            $query->where('driver_id', $this->selectedDriver);
        }

        $results = $query->orderBy($this->reportType === 'oficial' ? 'departure_datetime' : 'entry_at', 'desc')->paginate(15);

        return view('livewire.reports', [
            'results' => $results,
        ]);
    }
}
