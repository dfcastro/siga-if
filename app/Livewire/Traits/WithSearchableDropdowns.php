<?php

namespace App\Livewire\Traits;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\OfficialTrip;

trait WithSearchableDropdowns
{
    // Propriedades que o componente que usa este Trait deve ter
    public $driver_id = null;
    public string $driver_search = '';
    public $driver_results = [];
    public bool $show_driver_dropdown = false;
    
    public $vehicle_id = null;
    public string $vehicle_search = '';
    public $vehicle_results = [];
    public bool $show_vehicle_dropdown = false;

    // Lógica de busca para Motoristas
    public function updatedDriverSearch(string $value): void
    {
        if (strlen($value) >= 2) {
            $this->driver_results = Driver::where('name', 'like', '%' . $value . '%')
                ->where('is_authorized', true) // Pode ser específico de um componente, ajuste se necessário
                ->limit(5)
                ->get();
            $this->show_driver_dropdown = true;
        } else {
            $this->driver_results = collect();
            $this->show_driver_dropdown = false;
        }
        $this->driver_id = null; // Limpa o ID se o texto mudar
    }

    // Lógica de busca para Veículos Oficiais (exemplo)
    public function updatedVehicleSearch(string $value): void
    {
        if (strlen($value) >= 2) {
            $ongoingTripVehicleIds = OfficialTrip::whereNull('arrival_datetime')->pluck('vehicle_id');
            $this->vehicle_results = Vehicle::where('type', 'Oficial')
                ->whereNotIn('id', $ongoingTripVehicleIds)
                ->where(fn($q) => $q->where('model', 'like', "%{$value}%")->orWhere('license_plate', 'like', "%{$value}%"))
                ->limit(5)
                ->get();
            $this->show_vehicle_dropdown = true;
        } else {
            $this->vehicle_results = collect();
            $this->show_vehicle_dropdown = false;
        }
        $this->vehicle_id = null;
    }

    // Métodos para selecionar um resultado
    public function selectDriver($id, $name)
    {
        $this->driver_id = $id;
        $this->driver_search = $name;
        $this->show_driver_dropdown = false;
        $this->driver_results = collect();
    }
    
    public function selectVehicle($id, $text)
    {
        $this->vehicle_id = $id;
        $this->vehicle_search = $text;
        $this->show_vehicle_dropdown = false;
        $this->vehicle_results = collect();
    }
}