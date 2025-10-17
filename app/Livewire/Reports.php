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

    public function generateReport()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);
        $this->resetPage();
    }

    public function updatedReportType()
    {
        $this->reset('selectedVehicle', 'selectedDriver');
        $this->resetPage();
    }

    public function updating($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'selectedVehicle', 'selectedDriver'])) {
            $this->resetPage();
        }
    }

    // CORREÇÃO DE PERFORMANCE: Otimiza a busca de veículos para os filtros
    public function getVehiclesProperty()
    {
        $query = Vehicle::query()->select('id', 'model', 'license_plate');

        if ($this->reportType === 'oficial') {
            $query->where('type', 'Oficial');
        }

        return $query->orderBy('model')->get()->mapWithKeys(function ($vehicle) {
            return [$vehicle->id => "{$vehicle->model} ({$vehicle->license_plate})"];
        });
    }

    // CORREÇÃO DE PERFORMANCE: Otimiza a busca de motoristas para os filtros
    public function getDriversProperty()
    {
        $query = Driver::query()->select('id', 'name');

        if ($this->reportType === 'oficial') {
            $query->where('is_authorized', true);
        }

        return $query->orderBy('name')->pluck('name', 'id');
    }


    public function render()
    {
        // --- LÓGICA DO DASHBOARD DE ANALYTICS (SEM ALTERAÇÕES) ---
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
            // CORREÇÃO DE SEGURANÇA: Adiciona withTrashed para evitar erro com veículos/motoristas deletados
            $query = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                ->whereNotNull('arrival_datetime')
                ->whereBetween('departure_datetime', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);
        } else {
            $query = PrivateEntry::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
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
