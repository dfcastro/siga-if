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
            <div class="md:flex justify-between items-center mb-4">
                {{-- Campo de Busca --}}
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por nome ou documento..."
                    class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm mb-4 md:mb-0">

                {{-- ADICIONADO: Botões de Filtro --}}
                <div class="flex space-x-2">
                    <button wire:click="$set('filter', 'active')"
                        class="px-4 py-2 text-sm rounded-md shadow-sm border {{ $filter === 'active' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        Ativos
                    </button>
                    <button wire:click="$set('filter', 'trashed')"
                        class="px-4 py-2 text-sm rounded-md shadow-sm border {{ $filter === 'trashed' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                        Lixeira
                    </button>
                </div>
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
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($drivers as $driver)
                            <tr class="{{ $driver->trashed() ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 truncate max-w-sm" title="{{ $driver->name }}">{{ $driver->name }}
                                </td>
                                <td class="px-6 py-4">{{ $driver->document }}</td>
                                <td class="px-6 py-4 capitalize">{{ $driver->type }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs font-semibold rounded-full {{ $driver->is_authorized ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $driver->is_authorized ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2 text-center">
                                    {{-- AÇÕES CONDICIONAIS --}}
                                    @if ($driver->trashed())
                                        <button wire:click="restore({{ $driver->id }})"
                                            class="font-medium text-green-600 hover:text-green-800">Restaurar</button>
                                        <button wire:click="confirmForceDelete({{ $driver->id }})"
                                            class="font-medium text-red-600 hover:text-red-800 ml-2">Excluir
                                            Perm.</button>
                                    @else
                                        <button wire:click="showHistory({{ $driver->id }})"
                                            class="font-medium text-blue-600 hover:text-blue-800">Histórico</button>
                                        <x-secondary-button
                                            wire:click="edit({{ $driver->id }})">Editar</x-secondary-button>
                                        <x-danger-button
                                            wire:click="confirmDelete({{ $driver->id }})">Excluir</x-danger-button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum motorista
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Layout Tablet/Mobile: Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($drivers as $driver)
                    <div
                        class="bg-gray-50 border rounded-lg p-4 shadow-sm flex flex-col justify-between {{ $driver->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                        <div>
                            <h3 class="font-semibold text-lg truncate" title="{{ $driver->name }}">{{ $driver->name }}
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
                            {{-- AÇÕES CONDICIONAIS PARA O CARD --}}
                            @if ($driver->trashed())
                                <x-secondary-button class="flex-1"
                                    wire:click="restore({{ $driver->id }})">Restaurar</x-secondary-button>
                                <x-danger-button class="flex-1"
                                    wire:click="confirmForceDelete({{ $driver->id }})">Excluir
                                    Perm.</x-danger-button>
                            @else
                                <x-primary-button class="flex-1"
                                    wire:click="showHistory({{ $driver->id }})">Histórico</x-primary-button>
                                <x-secondary-button class="flex-1"
                                    wire:click="edit({{ $driver->id }})">Editar</x-secondary-button>
                                <x-danger-button class="flex-1"
                                    wire:click="confirmDelete({{ $driver->id }})">Excluir</x-danger-button>
                            @endif
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

    {{-- Modal de Edição/Criação (sem alterações, mantido como no seu original) --}}
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
                        {{-- Formulário de Nome, Documento, Tipo, etc. --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                            <input type="text" id="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm capitalize @error('name') border-red-500 @enderror"
                                wire:model="name" maxlength="100">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'fiscal')
                            <div class="flex items-center pt-2">
                                <input id="is_authorized" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    wire:model="is_authorized">
                                <label for="is_authorized" class="ml-2 block text-sm text-gray-900">Autorizado a
                                    dirigir frota oficial?</label>
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

    {{-- Modal de Confirmação de Exclusão (Atualizado) --}}
    @if ($isConfirmModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold">
                        {{ $filter === 'active' ? 'Mover para a Lixeira' : 'Confirmar Exclusão Permanente' }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Você tem certeza que deseja
                        <strong>{{ $filter === 'active' ? 'mover o motorista' : 'excluir PERMANENTEMENTE o motorista' }}</strong>
                        <strong>"{{ $driverNameToDelete }}"</strong>?
                    </p>
                    @if ($filter === 'trashed')
                        <p class="mt-1 text-sm text-red-600 font-bold">Esta ação não pode ser desfeita.</p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">Você poderá restaurar este item da lixeira.</p>
                    @endif
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                    <x-secondary-button wire:click="closeConfirmModal">Cancelar</x-secondary-button>
                    {{-- O clique do botão agora chama a função correta dependendo do filtro --}}
                    <x-danger-button wire:click="{{ $filter === 'active' ? 'deleteDriver' : 'forceDeleteDriver' }}">
                        Confirmar
                    </x-danger-button>
                </div>
            </div>
        </div>
    @endif

    @if ($isHistoryModalOpen && $driverForHistory)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Histórico de Movimentação</h3>
                    <p class="text-sm text-gray-600">{{ $driverForHistory->name }}</p>
                </div>

                <div class="p-6 overflow-y-auto">
                    @forelse ($driverHistory as $entry)
                        <div
                            class="border-l-4 {{ $entry['type'] === 'Oficial' ? 'border-blue-500' : 'border-green-500' }} pl-4 mb-4 pb-4 last:mb-0 last:pb-0">
                            <p class="font-semibold">Viagem {{ $entry['type'] }}</p>
                            <div class="text-sm text-gray-700 space-y-1 mt-1">
                                <p><strong>Veículo:</strong> {{ $entry['vehicle_info'] }}</p>
                                <p><strong>Data de Saída/Entrada:</strong>
                                    {{ \Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y H:i') }}</p>
                                <p><strong>Data de Chegada/Saída:</strong>
                                    {{ $entry['end_time'] ? \Carbon\Carbon::parse($entry['end_time'])->format('d/m/Y H:i') : 'Em trânsito' }}
                                </p>
                                <p><strong>{{ $entry['type'] === 'Oficial' ? 'Destino:' : 'Motivo:' }}</strong>
                                    {{ $entry['detail'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Nenhum histórico de movimentação para este condutor.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $driverHistory->links() }}
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 text-right mt-auto border-t">
                    <x-secondary-button wire:click="closeHistoryModal">Fechar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif
</div>
