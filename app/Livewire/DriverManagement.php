<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // IMPORTAR A PAGINAÇÃO
use App\Rules\Cpf;
use Illuminate\Support\Str;


#[Layout('layouts.app')]
class DriverManagement extends Component
{
    use WithPagination; // USAR A TRAIT

    // Suas propriedades existentes
    public string $name = '';
    public string $document = '';
    public string $type = 'Servidor';
    public $driverId;
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $driverIdToDelete;
    public $driverNameToDelete;
    public bool $is_authorized = true;

    // 3. ADICIONAR A PROPRIEDADE PARA A BUSCA
    public string $search = '';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Motoristas'];
    }

    // 4. ADICIONAR O MÉTODO PARA RESETAR A PÁGINA AO BUSCAR
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 5. ATUALIZAR A LÓGICA PARA BUSCAR E PAGINAR
        $query = Driver::query();

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $searchTerm = '%' . $this->search . '%';
                $subQuery->where('name', 'like', $searchTerm)
                    ->orWhere('document', 'like', $searchTerm);
            });
        }

        $drivers = $query->orderBy('name')->paginate(10);

        return view('livewire.driver-management', [
            'drivers' => $drivers,
        ]);
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            // SUBSTITUA 'cpf' PELA NOVA CLASSE
            'document' => ['required', new Cpf, Rule::unique('drivers')->ignore($this->driverId)],
            'type' => 'required',
            'is_authorized' => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.max' => 'O nome não pode ter mais de 100 caracteres.', // MENSAGEM PARA O LIMITE
        'document.required' => 'O campo documento é obrigatório.',
        'document.unique' => 'Este documento já está cadastrado.',
        'type.required' => 'O campo tipo é obrigatório.',
    ];

    public function store()
    {
        $this->validate();

        Driver::updateOrCreate(['id' => $this->driverId], [
            // 2. APLIQUE A CONVERSÃO AQUI
            'name' => Str::title($this->name), // Converte "joão da silva" para "João Da Silva"
            'document' => $this->document,
            'type' => $this->type,
            'is_authorized' => $this->is_authorized,
        ]);

        session()->flash('success', $this->driverId ? 'Motorista atualizado!' : 'Motorista cadastrado!');
        $this->closeModal();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        $this->driverId = $id;
        $this->name = $driver->name;
        $this->document = $driver->document;
        $this->type = $driver->type;
        $this->is_authorized = $driver->is_authorized;
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'document', 'type', 'driverId', 'is_authorized']);
        $this->type = 'Servidor';
        $this->is_authorized = true;
        $this->resetErrorBag();
    }

    public function deleteDriver()
    {
        Driver::find($this->driverIdToDelete)->delete();
        session()->flash('success', 'Motorista excluído com sucesso!');
        $this->closeConfirmModal();
    }

    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name;
        $this->isConfirmModalOpen = true;
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }
}
