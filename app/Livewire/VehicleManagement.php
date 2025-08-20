<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.app')]
class VehicleManagement extends Component
{
    // Propriedades para o formulário de edição/criação
    public string $license_plate = '';
    public string $model = '';
    public string $color = '';
    public $driver_id = '';
    public $vehicleId;
    public string $type = 'Particular';

    public bool $isModalOpen = false;

    // Propriedades para o modal de exclusão
    public bool $isConfirmModalOpen = false;
    public $vehicleIdToDelete;
    public $vehiclePlateToDelete;


    // Propriedade para a mensagem de sucesso
    public string $successMessage = '';

    // Envia o título da página para o layout
    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    // Define as regras de validação
    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|min:2',
            'color' => 'required|min:3',
            'driver_id' => 'required|exists:drivers,id',
            'type' => ['required', Rule::in(['Particular', 'Oficial'])], // NOVO: Regra de validação para o tipo
        ];
    }

    // Define as mensagens de erro customizadas
    protected $messages = [
        'license_plate.required' => 'O campo placa é obrigatório.',
        'license_plate.unique' => 'Esta placa já está cadastrada.',
        'driver_id.required' => 'É obrigatório selecionar um proprietário.',
    ];

    // Busca os dados e renderiza a view
    public function render()
    {
        $vehicles = Vehicle::with('driver')->orderBy('license_plate')->get();
        $drivers = Driver::orderBy('name')->get();

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    // Salva um novo registro ou atualiza um existente
    public function store()
    {
        $validatedData = $this->validate();

        // ADICIONE ESTA VERIFICAÇÃO DE SEGURANÇA
        // Se o usuário logado NÃO for admin ou fiscal E ele está tentando salvar como 'Oficial'
        if (!in_array(auth()->user()->role, ['admin', 'fiscal']) && $this->type === 'Oficial') {
            // Joga um erro de validação, impedindo o salvamento.
            throw ValidationException::withMessages([
                'type' => 'Você não tem permissão para cadastrar veículos oficiais.'
            ]);
        }

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => $this->license_plate,
            'model' => $this->model,
            'color' => $this->color,
            'driver_id' => $this->driver_id,
            'type' => $this->type,
        ]);

        session()->flash('success', $this->vehicleId ? 'Veículo atualizado com sucesso!' : 'Veículo cadastrado com sucesso!');
        $this->closeModal();
    }

    // Abre o modal para criar um novo registro
    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
        $this->dispatch('init-tom-select');
    }

    // Abre o modal para editar um registro existente
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleId = $id;
        $this->license_plate = $vehicle->license_plate;
        $this->model = $vehicle->model;
        $this->color = $vehicle->color;
        $this->driver_id = $vehicle->driver_id;
        $this->type = $vehicle->type; // ALTERADO: Carrega o tipo para edição

        $this->isModalOpen = true;
        $this->dispatch('init-tom-select', ['driverId' => $this->driver_id]);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset(['license_plate', 'model', 'color', 'driver_id', 'vehicleId', 'type']); // ALTERADO: Reseta o tipo
        $this->resetErrorBag();
        $this->dispatch('reset-tom-select');
    }


    // Abre o modal de confirmação de exclusão
    public function confirmDelete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleIdToDelete = $id;
        $this->vehiclePlateToDelete = $vehicle->license_plate;
        $this->isConfirmModalOpen = true;
    }

    // Exclui o veículo
    public function deleteVehicle()
    {
        $vehicle = Vehicle::find($this->vehicleIdToDelete);

        if ($vehicle) {
            $vehicle->delete();
            $this->successMessage = 'Veículo excluído com sucesso!';
        }

        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }
}
