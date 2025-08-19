<?php

namespace App\Livewire;

use App\Models\Driver;
use Livewire\Component;
use Illuminate\Validation\Rule; // Importante para a validação de 'unique' na edição

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
    public function render()
    {
        $drivers = Driver::orderBy('name')->get();
        return view('livewire.driver-management', ['drivers' => $drivers]);
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

        // Se tivermos um driverId, atualizamos. Senão, criamos.
        Driver::updateOrCreate(['id' => $this->driverId], [
            'name' => $this->name,
            'document' => $this->document,
            'type' => $this->type,
        ]);

        session()->flash('success', $this->driverId ? 'Motorista atualizado com sucesso!' : 'Motorista cadastrado com sucesso!');

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
        $this->reset(['name', 'document', 'type', 'driverId']);
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
