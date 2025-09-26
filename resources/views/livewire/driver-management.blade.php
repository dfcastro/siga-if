<div>
    {{-- Mensagens de Alerta --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Gerenciamento de Motoristas</h2>
            <x-primary-button wire:click="create">
                Cadastrar Novo Motorista
            </x-primary-button>
        </div>

        <div class="p-6">
            <div class="mb-4">

                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por nome ou documento..."
                    class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            {{-- Layout Desktop: Tabela --}}
            <div class="hidden lg:block">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autorizado
                                Frota?</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($drivers as $driver)
                            <tr>
                                <td class="px-6 py-4 truncate max-w-sm" title="{{ $driver->name }}">
                                    {{ $driver->name }}
                                </td>
                                <td class="px-6 py-4">{{ $driver->document }}</td>
                                <td class="px-6 py-4 capitalize">{{ $driver->type }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs font-semibold rounded-full {{ $driver->is_authorized ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $driver->is_authorized ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <x-secondary-button
                                        wire:click="edit({{ $driver->id }})">Editar</x-secondary-button>
                                    <x-danger-button
                                        wire:click="confirmDelete({{ $driver->id }})">Excluir</x-danger-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum motorista
                                    cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Layout Tablet/Mobile: Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($drivers as $driver)
                    <div class="bg-gray-50 border rounded-lg p-4 shadow-sm flex flex-col justify-between">
                        <div>
                            <h3 class="font-semibold text-lg truncate" title="{{ $driver->name }}">
                                {{ $driver->name }}
                            </h3>
                            <p class="text-sm text-gray-600">Documento: {{ $driver->document }}</p>
                            <p class="text-sm text-gray-600">Tipo: {{ $driver->type }}</p>
                            <p class="mt-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $driver->is_authorized ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                    {{ $driver->is_authorized ? 'Autorizado' : 'Não autorizado' }}
                                </span>
                            </p>
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <x-secondary-button class="flex-1"
                                wire:click="edit({{ $driver->id }})">Editar</x-secondary-button>
                            <x-danger-button class="flex-1"
                                wire:click="confirmDelete({{ $driver->id }})">Excluir</x-danger-button>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-2">Nenhum motorista cadastrado.</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>



    {{-- Modal de Edição/Criação --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isModalOpen') }"
            x-show="open" @keydown.escape.window="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.closeModal()">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">{{ $driverId ? 'Editar Motorista' : 'Cadastrar Novo Motorista' }}
                    </h3>
                </div>
                <form wire:submit="store">
                    <div class="p-6 space-y-4">
                        {{-- Nome --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" id="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm capitalize @error('name') border-red-500 @enderror"
                                wire:model="name" maxlength="100">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Documento COM MÁSCARA --}}
                        <div>
                            <label for="document" class="block text-sm font-medium text-gray-700">Documento
                                (CPF)</label>
                            <input type="text" id="document"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('document') border-red-500 @enderror"
                                wire:model="document" x-data x-mask="999.999.999-99">
                            @error('document')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipo --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select id="type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('type') border-red-500 @enderror"
                                wire:model="type">
                                <option value="Servidor">Servidor</option>
                                <option value="Aluno">Aluno</option>
                                <option value="Terceirizado">Terceirizado</option>
                                <option value="Visitante">Visitante</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Checkbox de permissão --}}
                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'fiscal')
                            <div class="flex items-center pt-2">
                                <input id="is_authorized" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    wire:model="is_authorized">
                                <label for="is_authorized" class="ml-2 block text-sm text-gray-900">
                                    Autorizado a dirigir frota oficial?
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                        <x-secondary-button type="button" wire:click="closeModal">Fechar</x-secondary-button>
                        <x-primary-button type="submit">{{ $driverId ? 'Atualizar' : 'Salvar' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de Confirmação de Exclusão --}}
    @if ($isConfirmModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isConfirmModalOpen') }"
            x-show="open" @keydown.escape.window="closeConfirmModal">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.away="closeConfirmModal">
                <div class="p-6">
                    <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
                    <p class="mt-2 text-sm text-gray-600">Você tem certeza que deseja excluir o motorista
                        <strong>{{ $driverNameToDelete }}</strong>?
                    </p>
                    <p class="mt-1 text-sm text-red-600">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center"
                        wire:click="deleteDriver" wire:loading.attr="disabled">
                        <svg wire:loading wire:target="deleteDriver"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="deleteDriver">Confirmar Exclusão</span>
                        <span wire:loading wire:target="deleteDriver">Excluindo...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
