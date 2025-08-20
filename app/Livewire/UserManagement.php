<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    public $name, $email, $role, $password, $userId;
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $userIdToDelete;
    public $userNameToDelete;
    public string $successMessage = '';

    public function layoutData() { return ['header' => 'Gerenciamento de Usuários']; }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    protected function rules() {
        return [
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role' => ['required', Rule::in(['admin', 'fiscal', 'porteiro'])],
            'password' => [$this->userId ? 'nullable' : 'required', 'min:8'],
        ];
    }

    public function create() { $this->resetInputFields(); $this->isModalOpen = true; }
    public function closeModal() { $this->isModalOpen = false; }
    private function resetInputFields(){ $this->reset(['name', 'email', 'role', 'password', 'userId', 'successMessage']); $this->resetErrorBag(); }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $data = ['name' => $validatedData['name'], 'email' => $validatedData['email'], 'role' => $validatedData['role']];

        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        User::updateOrCreate(['id' => $this->userId], $data);
        $this->successMessage = $this->userId ? 'Usuário atualizado com sucesso!' : 'Usuário criado com sucesso!';
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $user = User::findOrFail($id);
        $this->userIdToDelete = $id;
        $this->userNameToDelete = $user->name;
        $this->isConfirmModalOpen = true;
    }
    
    public function deleteUser()
    {
        // Impede que o usuário apague a si mesmo
        if ($this->userIdToDelete == auth()->id()) {
            session()->flash('error', 'Você não pode excluir sua própria conta.');
            $this->closeConfirmModal();
            return;
        }

        $user = User::find($this->userIdToDelete);
        if ($user) {
            $user->delete();
            $this->successMessage = 'Usuário excluído com sucesso!';
        }
        $this->closeConfirmModal();
    }

    public function closeConfirmModal() { $this->isConfirmModalOpen = false; }
}