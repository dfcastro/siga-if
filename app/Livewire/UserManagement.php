<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str; // <-- Necessário para gerar a senha aleatória do AD

#[Layout('layouts.app')]
class UserManagement extends Component
{
    // --- PROPRIEDADES EXISTENTES ---
    public $name, $email, $role, $password, $userId;
    public bool $isModalOpen = false;
    public bool $isConfirmModalOpen = false;
    public $userIdToDelete;
    public $userNameToDelete;
    public string $successMessage = '';
    public ?string $fiscal_type = null;

    // --- PROPRIEDADES PARA O AD ---
    public bool $isAdModalOpen = false;
    public string $adSearchTerm = '';
    public $adSearchResult = null;
    public string $adSelectedRole = 'porteiro';
    public string $adSelectedFiscalType = '';

    public function layoutData()
    {
        return ['header' => 'Gerenciamento de Usuários'];
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role' => ['required', Rule::in(['admin', 'fiscal', 'porteiro'])],
            'password' => [$this->userId ? 'nullable' : 'required', 'min:8'],
            'fiscal_type' => 'nullable|required_if:role,fiscal|in:official,private,both',
        ];
    }

    // =========================================================
    // MÉTODOS EXISTENTES (CRIAR, EDITAR, DELETAR LOCAL)
    // =========================================================

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'email', 'role', 'password', 'userId', 'successMessage', 'fiscal_type']);
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->fiscal_type = $user->fiscal_type;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'fiscal_type' => $this->role === 'fiscal' ? $this->fiscal_type : null,
        ];

        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        $this->successMessage = $this->userId ? 'Usuário atualizado com sucesso!' : 'Usuário criado com sucesso!';
        session()->flash('success', $this->successMessage);

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
            session()->flash('errorMessage', 'Você não pode excluir sua própria conta.');
            $this->closeConfirmModal();
            return;
        }

        $user = User::find($this->userIdToDelete);
        if ($user) {
            $user->delete();
            $this->successMessage = 'Usuário excluído com sucesso!';
            session()->flash('success', $this->successMessage);
        }
        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->isConfirmModalOpen = false;
    }

    // =========================================================
    // NOVOS MÉTODOS PARA INTEGRAÇÃO COM O ACTIVE DIRECTORY
    // =========================================================

    public function openAdModal()
    {
        $this->reset(['adSearchTerm', 'adSearchResult', 'adSelectedRole', 'adSelectedFiscalType']);
        $this->resetErrorBag();
        $this->isAdModalOpen = true;
    }

    public function closeAdModal()
    {
        $this->isAdModalOpen = false;
    }

    public function searchAdUser()
    {
        $this->validate(['adSearchTerm' => 'required|min:3']);
        $this->resetErrorBag();
        $this->adSearchResult = null;

        $username = $this->adSearchTerm;

        $ldap_host = config('services.ldap.host');
        $ldap_base_dn = config('services.ldap.base_dn');
        $ldap_bind_user = config('services.ldap.username');
        $ldap_bind_pass = config('services.ldap.password');

        if (!$ldap_host) {
            $this->addError('adSearchTerm', 'Servidor AD não configurado no arquivo .env do servidor.');
            return;
        }

        try {
            // Silencia os warnings nativos do PHP com o @
            $ldap_conn = @ldap_connect($ldap_host);
            if (!$ldap_conn) throw new \Exception("Falha ao conectar no servidor AD.");

            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);


            // Desiste se a rede não conectar em 3 segundos
            ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 3);
            // Desiste se o AD demorar mais de 3 segundos para responder à pesquisa
            ldap_set_option($ldap_conn, LDAP_OPT_TIMELIMIT, 3);

            if (!@ldap_bind($ldap_conn, $ldap_bind_user, $ldap_bind_pass)) {
                throw new \Exception("Falha na Autenticação com a conta de serviço do AD.");
            }

            // Procura pelo nome de login ou email do servidor
            $filter = "(|(sAMAccountName={$username})(mail={$username}))";
            $search = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
            $entries = @ldap_get_entries($ldap_conn, $search);

            if ($entries['count'] === 0) {
                $this->addError('adSearchTerm', 'Usuário não encontrado na base do Active Directory.');
                return;
            }

            // Extrai o Nome e Email
            // Extrai o Nome dando prioridade máxima ao DisplayName (Nome Real) e ignora o CN (CPF)
            $name = $entries[0]['displayname'][0]
                ?? $entries[0]['name'][0]
                ?? $entries[0]['description'][0]
                ?? $entries[0]['cn'][0]
                ?? 'Sem Nome Registrado';
            $email = $entries[0]['mail'][0] ?? $entries[0]['userprincipalname'][0] ?? "{$username}@ifnmg.edu.br";

            // Verifica se o utilizador já foi importado anteriormente
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $this->addError('adSearchTerm', "Este usuário já está cadastrado no sistema como: " . strtoupper($existingUser->role));
                return;
            }

            // Salva o resultado para mostrar na tela e aguardar a escolha do perfil
            $this->adSearchResult = [
                'name' => $name,
                'email' => $email,
            ];
        } catch (\Exception $e) {
            $this->addError('adSearchTerm', $e->getMessage());
        }
    }

    public function importAdUser()
    {
        if (!$this->adSearchResult) return;

        // Validamos apenas os campos do Modal do AD
        $this->validate([
            'adSelectedRole' => 'required|in:admin,fiscal,porteiro',
            'adSelectedFiscalType' => 'required_if:adSelectedRole,fiscal|nullable|in:official,private,both',
        ]);

        // Cria o usuário com uma senha "trancada", pois ele usará a do AD para logar
        User::create([
            'name' => $this->adSearchResult['name'],
            'email' => $this->adSearchResult['email'],
            'password' => bcrypt(Str::random(32)),
            'role' => $this->adSelectedRole,
            'fiscal_type' => $this->adSelectedRole === 'fiscal' ? $this->adSelectedFiscalType : null,
        ]);

        session()->flash('success', "Usuário {$this->adSearchResult['name']} importado do AD com sucesso!");
        $this->closeAdModal();
    }
}
