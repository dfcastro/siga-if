<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreatePrivateEntry extends Component
{
    public string $license_plate = '';
    public string $vehicle_model = '';
    public string $entry_reason = '';
    public $selected_driver_id = '';
    public bool $isNewVisitor = false;
    public string $visitor_document = '';
    public string $other_reason = '';
    public array $predefinedReasons = [
        'Entrada de Servidor',
        'Reunião',
        'Entrega de Material',
        'Visita Técnica',
        'Evento',
        'Prestação de Serviço',
        'Pais de aluno, buscar aluno, trazer aluno,etc',
    ];
    public string $successMessage = '';
    public string $exitSearch = '';
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null;

    public function render()
    {
        // Busca os veículos que ESTÃO ATUALMENTE no pátio
        $currentVehicles = PrivateEntry::with(['vehicle.driver', 'driver'])
            ->whereNull('exit_at') // 1. Condição principal: Garante que NUNCA buscará veículos que já saíram.
            ->when($this->exitSearch, function ($query) {
                $searchTerm = '%' . $this->exitSearch . '%';
                // 2. Agrupa as condições de busca. A busca só será feita DENTRO
                //    do universo de veículos que já atendem à condição principal.
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('license_plate', 'like', $searchTerm) // Busca pela placa
                        ->orWhereHas('driver', function ($driverQuery) use ($searchTerm) {
                            $driverQuery->where('name', 'like', $searchTerm); // OU busca pelo nome do motorista
                        });
                });
            })
            ->latest('entry_at')
            ->get();

        $drivers = Driver::orderBy('name')->get();

        return view('livewire.create-private-entry', [
            'currentVehicles' => $currentVehicles,
            'drivers' => $drivers
        ]);
    }

    public function updatedSearch($value)
    {
        if (strlen($value) < 3) {
            $this->searchResults = collect();
            return;
        }

        $vehiclesFound = Vehicle::with('driver')
            ->where('license_plate', 'like', '%' . $value . '%')
            ->orWhere('model', 'like', '%' . $value . '%')
            ->get();

        $driversFound = Driver::with('vehicles')
            ->where('name', 'like', '%' . $value . '%')
            ->get();

        $formattedResults = collect();

        foreach ($vehiclesFound as $vehicle) {
            $formattedResults->push([
                'id' => $vehicle->id,
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Prop.: {$vehicle->driver->name}"
            ]);
        }

        foreach ($driversFound as $driver) {
            foreach ($driver->vehicles as $vehicle) {
                $formattedResults->push([
                    'id' => $vehicle->id,
                    'text' => "MOTORISTA: {$driver->name} - Veículo: {$vehicle->license_plate} ({$vehicle->model})"
                ]);
            }
        }

        $this->searchResults = $formattedResults->unique('id')->sortBy('text');
    }

    public function selectVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $this->selectedVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate; // @entangle cuida do Alpine
        $this->vehicle_model = $vehicle->model;
        $this->selected_driver_id = $vehicle->driver_id;

        $this->search = '';
        $this->searchResults = [];

        $this->dispatch('set-driver-select', $this->selected_driver_id);
    }

    public function updatedSelectedDriverId($value)
    {
        $this->isNewVisitor = !is_numeric($value) && !empty($value);
    }

    public function save()
    {
        $this->validate([
            'license_plate' => ['required', 'min:7', 'regex:/^[A-Z]{3}-\d{4}$|^[A-Z]{3}\d[A-Z]\d{2}$/', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'selected_driver_id' => 'required',
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            'visitor_document' => ['required_if:isNewVisitor,true', 'nullable', 'unique:drivers,document'],
        ], [
            'license_plate.unique' => 'Esta placa já consta como dentro do campus.',
            'other_reason.required_if' => 'Por favor, especifique o motivo da entrada.',
            'visitor_document.required_if' => 'O CPF do visitante é obrigatório.',
            'visitor_document.unique' => 'Este CPF já está cadastrado.'
        ]);

        $driverId = $this->selected_driver_id;
        $finalReason = $this->entry_reason === 'Outro' ? $this->other_reason : $this->entry_reason;

        if ($this->isNewVisitor) {
            $newDriver = Driver::create([
                'name' => $this->selected_driver_id,
                'document' => $this->visitor_document,
                'type' => 'Visitante',
                'is_authorized' => false,
            ]);
            $driverId = $newDriver->id;
        }

        PrivateEntry::create([
            'license_plate' => $this->license_plate,
            'vehicle_model' => $this->vehicle_model,
            'entry_reason' => $finalReason,
            'entry_at' => now(),
            'guard_on_entry' => auth()->user()->name,
            'vehicle_id' => $this->selectedVehicleId,
            'driver_id' => $driverId,
        ]);

        $this->successMessage = 'Entrada do veículo ' . $this->license_plate . ' registrada com sucesso!';
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'license_plate',
            'vehicle_model',
            'entry_reason',
            'other_reason',
            'search',
            'searchResults',
            'selectedVehicleId',
            'selected_driver_id',
            'isNewVisitor',
            'visitor_document'
        ]);
        $this->dispatch('reset-form-fields');
    }

    public function registerExit($entryId)
    {
        $entry = PrivateEntry::find($entryId);
        if ($entry) {
            $entry->exit_at = now();
            $entry->guard_on_exit = auth()->user()->name;
            $entry->save();

            $this->successMessage = 'Saída do veículo ' . ($entry->vehicle->license_plate ?? $entry->license_plate) . ' registrada com sucesso!';
        }
    }
}
