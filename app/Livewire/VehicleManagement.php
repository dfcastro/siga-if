<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Livewire\WithPagination;


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

    public string $search = ''; //PROPRIEDADE PARA A BUSCA

    use WithPagination; // USA A TRAIT DE PAGINAÇÃO


    // PROPRIEDADE PARA A LISTA DE CORES
    public array $commonColors = [
        'PRETO',
        'BRANCO',
        'PRATA',
        'CINZA',
        'VERMELHO',
        'AZUL',
        'VERDE',
        'AMARELO',
        'DOURADO',
        'MARROM',
        'BEGE',
        'LARANJA',
        'ROXO'
    ];


    // Envia o título da página para o layout
    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Veículos'];
    }

    //MÉTODO PARA RESETAR A PÁGINA AO BUSCAR
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Define as regras de validação
    protected function rules()
    {
        return [
            'license_plate' => ['required', 'min:7', Rule::unique('vehicles')->ignore($this->vehicleId)],
            'model' => 'required|min:2',
            'color' => 'required|min:3',
            'driver_id' => 'required_if:type,Particular|nullable|exists:drivers,id',
            'type' => ['required', Rule::in(['Particular', 'Oficial'])], // Regra de validação para o tipo
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
        // LÓGICA DE BUSCA E PAGINAÇÃO
        $query = Vehicle::with('driver');

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('license_plate', 'like', $searchTerm)
                    ->orWhere('model', 'like', $searchTerm)
                    ->orWhere('color', 'like', $searchTerm)
                    ->orWhereHas('driver', function ($driverQuery) use ($searchTerm) {
                        $driverQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        // CORREÇÃO PRINCIPAL: Trocar ->get() por ->paginate()
        $vehicles = $query->orderBy('model', 'asc')->paginate(10);

        $drivers = Driver::orderBy('name')->get();

        return view('livewire.vehicle-management', [
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    // Salva um novo registro ou atualiza um existente
    public function store()
    {
        $this->validate([
            'license_plate' => [
                'required',
                'string',
                'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$|^[A-Z]{3}-[0-9]{4}$/i',
                Rule::unique('vehicles')->ignore($this->vehicleId)
            ],
            // DICA APLICADA: Limite de 20 caracteres
            'model' => 'required|string|max:25',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:Oficial,Particular',
            'driver_id' => 'nullable|exists:drivers,id',
        ], [
            'license_plate.regex' => 'O formato da placa é inválido. Use AAA-1234 ou ABC1D23.'
        ]);

        // CORREÇÃO DO ERRO SQL: Converte string vazia para null
        $driverId = $this->driver_id === '' ? null : $this->driver_id;

        Vehicle::updateOrCreate(['id' => $this->vehicleId], [
            'license_plate' => strtoupper($this->license_plate),
            'model'         => Str::upper($this->model),
            'color'         => Str::upper($this->color),
            'type' => $this->type,
            'driver_id' => $driverId,
        ]);

        session()->flash('successMessage', $this->vehicleId ? 'Veículo atualizado com sucesso!' : 'Veículo criado com sucesso!');

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
        $this->resetInputFields();
        $this->dispatch('destroy-tom-select');
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
