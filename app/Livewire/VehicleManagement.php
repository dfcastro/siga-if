<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;

class VehicleManagement extends Component
{
    // Propriedades para o formulário de edição/criação
    public string $license_plate = '';
    public string $model = '';
    public string $color = '';
    public $driver_id = '';
    public $vehicleId;
    public bool $isModalOpen = false;

    public string $successMessage = '';

    // Propriedades para o modal de exclusão
    public bool $isConfirmModalOpen = false;
    public $vehicleIdToDelete;
    public $vehiclePlateToDelete;

    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|min:2',
            'color' => 'min:3',
            'driver_id' => 'required|exists:drivers,id',
        ];
    }

    protected $messages = [
        'license_plate.required' => 'O campo placa é obrigatório.',
        'license_plate.unique' => 'Esta placa já está cadastrada.',
        'driver_id.required' => 'É obrigatório selecionar um proprietário.',
    ];

    public function render()
    {
        $vehicles = Vehicle::with('driver')->orderBy('license_plate')->get();
        $drivers = Driver::orderBy('name')->get();

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    public function store()
    {
        $this->validate();

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => $this->license_plate,
            'model' => $this->model,
            'color' => $this->color,
            'driver_id' => $this->driver_id,
        ]);

        // TROQUE a linha da session()->flash() por esta:
        $this->successMessage = $this->vehicleId ? 'Veículo atualizado com sucesso!' : 'Veículo cadastrado com sucesso!';

        $this->closeModal();
    }

    // No método create()
    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
        $this->dispatch('init-tom-select'); // Adicione esta linha
    }

    // No método edit($id)
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleId = $id;
        $this->license_plate = $vehicle->license_plate;
        $this->model = $vehicle->model;
        $this->color = $vehicle->color;
        $this->driver_id = $vehicle->driver_id;

        $this->isModalOpen = true;
        // Envia um evento para o JS para definir o valor inicial E inicializar o Tom Select
        $this->dispatch('init-tom-select', driverId: $this->driver_id); // Altere esta linha
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['license_plate', 'model', 'color', 'driver_id', 'vehicleId']);
        $this->resetErrorBag();
        // Envia um evento para o JS para limpar o Tom Select
        $this->dispatch('reset-tom-select');
    }

    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    public function deleteVehicle()
    {
        $vehicle = Vehicle::find($this->vehicleIdToDelete);

        if ($vehicle) {
            $vehicle->delete();
            session()->flash('success', 'Veículo excluído com sucesso!');
        }

        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }
}
