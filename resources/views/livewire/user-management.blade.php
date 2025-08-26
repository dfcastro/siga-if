<div>
    {{-- Mensagem de Sucesso --}}
    @if ($successMessage)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ $successMessage }}</span>
    </div>
    @endif
    @if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Gerenciamento de Usuários</h2>
            <button wire:click="create" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Criar Novo Usuário
            </button>
        </div>

        <div class="p-6">
            {{-- Tabela Responsiva --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800 text-capitalize">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit({{ $user->id }})" class="px-3 py-1 bg-gray-600 text-white rounded-md hover:bg-gray-700">Editar</button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 ml-2">Excluir</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum usuário cadastrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Edição/Criação --}}
    @if($isModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isModalOpen') }" x-show="open" @keydown.escape.window="open = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="open = false">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">{{ $userId ? 'Editar Usuário' : 'Criar Novo Usuário' }}</h3>
            </div>
            <form wire:submit="store">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" wire:model="name">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror" wire:model="email">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror" wire:model="role">
                            <option value="">Selecione um cargo</option>
                            <option value="admin">Admin</option>
                            <option value="fiscal">Fiscal</option>
                            <option value="porteiro">Porteiro</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror" wire:model="password">
                        <p class="text-xs text-gray-500 mt-1">{{ $userId ? 'Deixe em branco para não alterar a senha.' : '' }}</p>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" @click="open = false">Fechar</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ $userId ? 'Atualizar Usuário' : 'Salvar Usuário' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal de Confirmação de Exclusão --}}
    @if($isConfirmModalOpen)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isConfirmModalOpen') }" x-show="open" @keydown.escape.window="open = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.away="open = false">
            <div class="p-6">
                <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
                <p class="mt-2 text-sm text-gray-600">Você tem certeza que deseja excluir o usuário <strong>{{ $userNameToDelete }}</strong>?</p>
                <p class="mt-1 text-sm text-red-600">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" @click="open = false">Cancelar</button>
                <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" wire:click="deleteUser">Confirmar Exclusão</button>
            </div>
        </div>
    </div>
    @endif
</div>