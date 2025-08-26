<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Reports extends Component
{
    use WithPagination;

    // Propriedades para os filtros (isso está correto)
    public $reportType = 'oficial';
    public $startDate;
    public $endDate;
    public $selectedVehicle = '';
    public $selectedDriver = '';

    // A propriedade pública de resultados foi REMOVIDA
    // public $results = []; // <<-- REMOVIDO

    // Para popular os dropdowns de filtro
    public $vehicles;
    public $drivers;

    public function layoutData()
    {
        return ['header' => 'Relatórios de Viagens'];
    }

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->vehicles = Vehicle::orderBy('model')->get();
        $this->drivers = Driver::orderBy('name')->get();
    }
    
    // Este método agora só serve para o botão e para validar.
    // A busca real foi movida para o render().
    public function generateReport()
    {
        // Valida os dados antes de permitir que o render() faça a busca
        $this->validate([
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);

        // Reseta a paginação para a primeira página a cada nova busca
        $this->resetPage();
    }

    // Este hook também reseta a página quando o tipo de relatório muda
    public function updatingReportType()
    {
        $this->resetPage();
    }

    public function render()
    {
        // A LÓGICA DE BUSCA E PAGINAÇÃO AGORA VIVE AQUI
        $query = null;
        if ($this->reportType === 'oficial') {
            $query = OfficialTrip::with(['vehicle', 'driver'])
                ->whereNotNull('arrival_datetime')
                ->whereBetween('departure_datetime', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);

            if ($this->selectedVehicle) {
                $query->where('vehicle_id', $this->selectedVehicle);
            }
            if ($this->selectedDriver) {
                $query->where('driver_id', $this->selectedDriver);
            }
            $results = $query->orderBy('departure_datetime', 'desc')->paginate(15);

        } else { // 'particular'
            $query = PrivateEntry::with(['vehicle', 'driver'])
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$this->startDate, Carbon::parse($this->endDate)->endOfDay()]);

            if ($this->selectedVehicle) {
                $query->where('vehicle_id', $this->selectedVehicle);
            }
            if ($this->selectedDriver) {
                $query->where('driver_id', $this->selectedDriver);
            }
            $results = $query->orderBy('entry_at', 'desc')->paginate(15);
        }

        return view('livewire.reports', [
            'results' => $results, // Passa o paginator diretamente para a view
        ]);
    }
}