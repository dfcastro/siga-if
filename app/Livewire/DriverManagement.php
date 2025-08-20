<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Illuminate\Validation\Rule; // Importante para a validação de 'unique' na edição
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DriverManagement extends Component
{
    // Propriedades para o formulário do modal
    public string $name = '';
    public string $document = '';
    public string $type = '';

    // Nova propriedade para guardar o ID do motorista em edição
    public $driverId;

    // Propriedade para controlar a visibilidade do modal
    public bool $isModalOpen = false;

    // NOVAS propriedades para o modal de exclusão
    public bool $isConfirmModalOpen = false;
    public $driverIdToDelete;
    public $driverNameToDelete;
    // Método que renderiza a view
    public bool $is_authorized = false;

    public function layoutData()
    {
        return [
            'header' => 'Gerenciamento de Motoristas',
        ];
    }

    public function render()
    {
        $drivers = Driver::orderBy('name')->get();
        // Ponto de verificação #3: O return deve ser simples, SEM o ->layout()
        return view('livewire.driver-management', [
            'drivers' => $drivers,
        ]);
    }

    // Regras de validação
    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            // Regra 'document' atualizada: deve ser único, ignorando o ID do motorista atual em edição
            'document' => ['required', Rule::unique('drivers')->ignore($this->driverId)],
            'type' => 'required',
        ];
    }

    // Customiza as mensagens de erro (opcional, mas bom ter)
    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'document.required' => 'O campo documento é obrigatório.',
        'document.unique' => 'Este documento já está cadastrado.',
        'type.required' => 'O campo tipo é obrigatório.',
    ];


    // Método chamado para criar ou atualizar
    public function store()
    {
        $this->validate();

        Driver::updateOrCreate(['id' => $this->driverId], [
            'name' => $this->name,
            'document' => $this->document,
            'type' => $this->type,
            'is_authorized' => $this->is_authorized, // Adicione esta linha
        ]);

        session()->flash('success', $this->driverId ? 'Motorista atualizado!' : 'Motorista cadastrado!');
        $this->closeModal();
    }

    // Método para abrir o modal para CRIAR
    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    // NOVO MÉTODO para abrir o modal para EDITAR
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

    // Método para fechar o modal
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    // Método auxiliar para limpar os campos
    private function resetInputFields()
    {
        $this->reset(['name', 'document', 'type', 'driverId', 'is_authorized']); // Adicione 'is_authorized'
        $this->resetErrorBag();
    }

    public function deleteDriver()
    {
        $driver = Driver::find($this->driverIdToDelete);

        if ($driver) {
            $driver->delete();
            session()->flash('success', 'Motorista excluído com sucesso!');
        }

        // Fecha o modal de confirmação após excluir
        $this->closeConfirmModal();
    }

    // NOVO MÉTODO para abrir o modal de confirmação
    public function confirmDelete($id)
    {
        $driver = Driver::findOrFail($id);
        $this->driverIdToDelete = $id;
        $this->driverNameToDelete = $driver->name; // Guardamos o nome para exibir no modal
        $this->isConfirmModalOpen = true;
    }

    // NOVO MÉTODO para fechar o modal de confirmação
    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }
}
