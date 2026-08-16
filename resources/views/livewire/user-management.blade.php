<div>
    {{-- Alertas Estilizados --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity.duration.500ms
            class="bg-green-50 border border-green-200 border-l-4 border-l-green-500 text-green-800 p-4 rounded-xl relative mb-6 shadow-sm flex items-center"
            role="alert">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm uppercase tracking-wider text-green-900">Sucesso</p>
                <p class="text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if (session('errorMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity.duration.500ms
            class="bg-red-50 border border-red-200 border-l-4 border-l-red-500 text-red-800 p-4 rounded-xl relative mb-6 shadow-sm flex items-center"
            role="alert">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm uppercase tracking-wider text-red-900">Atenção</p>
                <p class="text-sm mt-0.5">{{ session('errorMessage') }}</p>
            </div>
        </div>
    @endif

    {{-- Cabeçalho e Botões --}}
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">Gerenciamento de Usuários</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Controle de acessos e permissões do SIGA-IF.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            {{-- Botão do Active Directory --}}
            <button wire:click="openAdModal"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-blue-50 border border-blue-200 px-6 py-3 text-sm font-black text-blue-700 shadow-sm hover:bg-blue-100 transition-all transform active:scale-95 tracking-wide">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                    </path>
                </svg>
                IMPORTAR DO AD
            </button>

            {{-- Botão Novo Usuário Manual --}}
            <button wire:click="create"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-ifnmg-green px-6 py-3 text-sm font-black text-white shadow-md hover:bg-green-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-offset-2 transition-all transform active:scale-95 tracking-wide">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                NOVO USUÁRIO
            </button>
        </div>
    </div>

    {{-- Card Principal (Tabela) --}}
    <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-100">
        <div class="p-0 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 hidden sm:table-header-group">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Usuário</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Perfil de Acesso</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr
                                class="hover:bg-gray-50 transition-colors flex flex-col sm:table-row py-4 sm:py-0 border-b sm:border-0 last:border-0">

                                {{-- Linha Mobile: Título --}}
                                <td class="px-6 py-2 sm:py-4 flex sm:table-cell justify-between items-center sm:block">
                                    <span class="sm:hidden font-bold text-xs text-gray-500 uppercase">Usuário:</span>
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                </td>

                                {{-- Linha Mobile: Email --}}
                                <td class="px-6 py-2 sm:py-4 flex sm:table-cell justify-between items-center sm:block">
                                    <span class="sm:hidden font-bold text-xs text-gray-500 uppercase">Email:</span>
                                    <div class="text-sm font-mono text-gray-600">{{ $user->email }}</div>
                                </td>

                                {{-- Linha Mobile: Perfil --}}
                                <td class="px-6 py-2 sm:py-4 flex sm:table-cell justify-between items-center sm:block">
                                    <span class="sm:hidden font-bold text-xs text-gray-500 uppercase">Perfil:</span>
                                    <div class="flex flex-col items-end sm:items-start gap-1">
                                        <span
                                            class="px-2.5 py-1 inline-flex text-[10px] uppercase tracking-wider font-bold rounded-md bg-gray-100 text-gray-800 border border-gray-200 shadow-sm">
                                            {{ $user->role }}
                                        </span>
                                        @if ($user->role === 'fiscal' && $user->fiscal_type)
                                            <span
                                                class="px-2 py-0.5 text-[9px] uppercase tracking-wider font-bold rounded bg-blue-50 text-blue-700 border border-blue-200">
                                                Frota:
                                                {{ $user->fiscal_type === 'both' ? 'Ambas' : ($user->fiscal_type === 'official' ? 'Oficial' : 'Privada') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Linha Mobile: Ações --}}
                                <td
                                    class="px-6 py-3 sm:py-4 sm:text-right mt-2 sm:mt-0 bg-gray-50 sm:bg-transparent flex justify-end gap-2">
                                    <button wire:click="edit({{ $user->id }})" title="Editar"
                                        class="inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-bold text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }})" title="Excluir"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-50 px-3 py-2 text-sm font-bold text-red-600 border border-red-200 shadow-sm hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Nenhum usuário cadastrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL DE IMPORTAÇÃO DO ACTIVE DIRECTORY --}}
    {{-- ======================================================== --}}
    @if ($isAdModalOpen)
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">

                <div
                    class="px-6 py-5 border-b border-blue-200 bg-blue-50 flex justify-between items-center rounded-t-2xl">
                    <h3 class="text-xl font-black text-blue-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                            </path>
                        </svg>
                        Importar Utilizador do AD
                    </h3>
                    <button wire:click="closeAdModal" class="text-blue-400 hover:text-blue-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto">
                    {{-- ETAPA 1: BUSCA --}}
                    <div class="mb-6">
                        <label for="adSearchTerm"
                            class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Login de Rede
                            ou Email</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="adSearchTerm" wire:model="adSearchTerm"
                                wire:keydown.enter="searchAdUser"
                                class="block w-full bg-gray-50 focus:bg-white rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3"
                                placeholder="ex: daniel.castro ou 1122334">
                            <button wire:click="searchAdUser" wire:loading.attr="disabled" wire:target="searchAdUser"
                                type="button"
                                class="w-full sm:w-auto flex-shrink-0 inline-flex justify-center items-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-md hover:bg-blue-700 transition-all">
                                <svg wire:loading wire:target="searchAdUser"
                                    class="animate-spin mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                BUSCAR
                            </button>
                        </div>
                        <x-input-error for="adSearchTerm" class="mt-2 font-semibold" />
                    </div>

                    {{-- ETAPA 2: RESULTADO E DEFINIÇÃO DE PERFIL --}}
                    @if ($adSearchResult)
                        <div class="animate-fade-in border-t border-gray-200 pt-6">
                            <div class="bg-green-50 p-4 rounded-xl border border-green-200 mb-6">
                                <p
                                    class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Encontrado no AD
                                </p>
                                <p class="font-bold text-gray-900 text-lg">{{ $adSearchResult['name'] }}</p>
                                <p class="text-sm font-mono text-gray-600">{{ $adSearchResult['email'] }}</p>
                            </div>

                            <div class="space-y-5" x-data="{ role: @entangle('adSelectedRole').live }">
                                <div>
                                    <label
                                        class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Perfil
                                        no Sistema</label>
                                    <select wire:model.live="adSelectedRole" x-model="role"
                                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 bg-gray-50 focus:bg-white">
                                        <option value="porteiro">Porteiro (Controla Cancelas)</option>
                                        <option value="fiscal">Fiscal (Aprova Relatórios e Frotas)</option>
                                        <option value="admin">Administrador (Acesso Total)</option>
                                    </select>
                                    <x-input-error for="adSelectedRole" class="mt-2 font-semibold" />
                                </div>

                                <div x-show="role === 'fiscal'" x-transition
                                    class="bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                                    <label
                                        class="font-bold text-yellow-800 uppercase tracking-wider text-xs mb-2 block">Qual
                                        tipo de frota este fiscal gerencia?</label>
                                    <select wire:model="adSelectedFiscalType"
                                        class="block w-full border-yellow-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 py-2.5 bg-white text-sm">
                                        <option value="">Selecione...</option>
                                        <option value="official">Apenas Frota Oficial (Veículos do Campus)</option>
                                        <option value="private">Apenas Portaria (Particulares e Visitantes)</option>
                                        <option value="both">Ambas (Diretor / Chefia)</option>
                                    </select>
                                    <x-input-error for="adSelectedFiscalType" class="mt-2 font-semibold" />
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div
                    class="px-6 py-5 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse justify-start gap-3 border-t border-gray-200 rounded-b-2xl">
                    @if ($adSearchResult)
                        <button wire:click="importAdUser" wire:loading.attr="disabled" type="button"
                            class="w-full sm:w-auto flex justify-center items-center rounded-xl bg-ifnmg-green px-8 py-3.5 text-sm font-black text-white shadow-md hover:bg-green-700 transition-all active:scale-95 uppercase tracking-wide">
                            CONFIRMAR IMPORTAÇÃO
                        </button>
                    @endif
                    <button type="button" wire:click="closeAdModal"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all uppercase tracking-wide">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- MODAL EDIÇÃO E CRIAÇÃO MANUAL --}}
    {{-- ======================================================== --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">

                <div
                    class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-2xl">
                    <h3 class="text-xl font-black text-gray-800">
                        {{ $userId ? 'Editar Usuário' : 'Novo Usuário Manual' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 sm:p-8 overflow-y-auto max-h-[70vh]">
                    <form wire:submit="store" id="userForm" class="space-y-5" x-data="{ role: @entangle('role').live }">
                        <div>
                            <label for="name"
                                class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Nome</label>
                            <input type="text" id="name" wire:model="name"
                                class="block w-full bg-gray-50 focus:bg-white rounded-xl border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green py-3">
                            <x-input-error for="name" class="mt-2 font-semibold" />
                        </div>
                        <div>
                            <label for="email"
                                class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Email</label>
                            <input type="email" id="email" wire:model="email"
                                class="block w-full bg-gray-50 focus:bg-white rounded-xl border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green py-3 font-mono">
                            <x-input-error for="email" class="mt-2 font-semibold" />
                        </div>
                        <div>
                            <label for="role"
                                class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Cargo /
                                Perfil</label>
                            <select id="role" wire:model.live="role" x-model="role"
                                class="block w-full bg-gray-50 focus:bg-white rounded-xl border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green py-3">
                                <option value="">Selecione um cargo</option>
                                <option value="admin">Administrador Geral</option>
                                <option value="fiscal">Fiscal</option>
                                <option value="porteiro">Porteiro</option>
                            </select>
                            <x-input-error for="role" class="mt-2 font-semibold" />
                        </div>

                        <div x-show="role === 'fiscal'" x-transition
                            class="bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                            <label
                                class="font-bold text-yellow-800 uppercase tracking-wider text-xs mb-2 block">Especialidade
                                do Fiscal</label>
                            <select wire:model="fiscal_type" id="fiscal_type"
                                class="block w-full border-yellow-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 py-2.5 bg-white text-sm">
                                <option value="">Selecione a especialidade</option>
                                <option value="official">Frota Oficial (Veículos do IF)</option>
                                <option value="private">Portaria (Particulares e Visitantes)</option>
                                <option value="both">Ambos os Tipos (Chefia)</option>
                            </select>
                            <x-input-error for="fiscal_type" class="mt-2 font-semibold" />
                        </div>

                        <div>
                            <label for="password"
                                class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2 block">Senha de
                                Acesso Local</label>
                            <input type="password" id="password" wire:model="password"
                                class="block w-full bg-gray-50 focus:bg-white rounded-xl border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green py-3">
                            <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wide">
                                {{ $userId ? 'Deixe em branco para manter a senha atual.' : 'Mínimo de 8 caracteres.' }}
                            </p>
                            <x-input-error for="password" class="mt-2 font-semibold" />
                        </div>
                    </form>
                </div>

                <div
                    class="px-6 py-5 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse justify-start gap-3 border-t border-gray-200 rounded-b-2xl">
                    <button type="submit" form="userForm" wire:loading.attr="disabled"
                        class="w-full sm:w-auto flex justify-center items-center rounded-xl bg-ifnmg-green px-8 py-3.5 text-sm font-black text-white shadow-md hover:bg-green-700 transition-all active:scale-95 uppercase tracking-wide">
                        {{ $userId ? 'Atualizar' : 'Salvar Manualmente' }}
                    </button>
                    <button type="button" wire:click="closeModal"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all uppercase tracking-wide">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- MODAL CONFIRMAÇÃO DE EXCLUSÃO --}}
    {{-- ======================================================== --}}
    @if ($isConfirmModalOpen)
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 sm:p-8 flex flex-col items-center text-center">
                    <div
                        class="flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 shadow-inner">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Excluir Conta</h3>
                    <p class="text-sm text-gray-600 mb-2">Você tem certeza que deseja remover o acesso de <br><strong
                            class="text-gray-900 text-base">"{{ $userNameToDelete }}"</strong>?</p>
                    <p class="text-xs text-red-600 font-bold bg-red-50 p-2 rounded mt-2 border border-red-100">Esta
                        ação bloqueará a entrada desta pessoa no sistema imediatamente.</p>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse justify-center gap-3 border-t border-gray-200">
                    <button wire:click="deleteUser"
                        class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all active:scale-95">
                        Confirmar
                    </button>
                    <button wire:click="closeConfirmModal"
                        class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all active:scale-95">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
