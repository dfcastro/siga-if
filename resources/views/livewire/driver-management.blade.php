<div>
    {{-- Alerta de Sucesso --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 shadow-md"
            role="alert">
            <div class="flex">
                <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg></div>
                <div>
                    <p class="font-bold">Sucesso!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if (session('errorMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 shadow-md"
            role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Atenção!</p>
                    <p class="text-sm">{{ session('errorMessage') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Gerenciamento de Motoristas</h2>
                <p class="text-sm text-gray-500 mt-1">Adicione, edite e visualize todos os motoristas cadastrados.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-primary-button wire:click="create">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Novo Motorista
                </x-primary-button>
            </div>
        </div>

        <div class="p-6">
            {{-- Controles de Busca e Filtro --}}
            <div class="md:flex justify-between items-center mb-6">
                <div class="relative w-full md:w-1/3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Buscar por nome ou documento..."
                        class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:border-ifnmg-green focus:ring-ifnmg-green mb-4 md:mb-0">
                </div>
                <div class="flex space-x-2 rounded-md shadow-sm" role="group">
                    <button wire:click="$set('filter', 'active')" type="button"
                        class="px-4 py-2 text-sm font-medium {{ $filter === 'active' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-900 hover:bg-gray-50' }} border border-gray-200 rounded-l-lg focus:z-10 focus:ring-2 focus:ring-ifnmg-green">
                        Ativos
                    </button>
                    <button wire:click="$set('filter', 'trashed')" type="button"
                        class="px-4 py-2 text-sm font-medium {{ $filter === 'trashed' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-900 hover:bg-gray-50' }} border-t border-b border-r border-gray-200 rounded-r-md focus:z-10 focus:ring-2 focus:ring-ifnmg-green">
                        Lixeira
                    </button>
                </div>
            </div>

            {{-- Tabela Desktop --}}
            <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autorizado
                                Frota?</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($drivers as $driver)
                            <tr
                                class="{{ $driver->trashed() ? 'bg-red-50' : 'odd:bg-white even:bg-gray-50' }} hover:bg-blue-50">
                                <td class="px-6 py-4 truncate max-w-sm" title="{{ $driver->name }}">{{ $driver->name }}
                                </td>
                                <td class="px-6 py-4">{{ $driver->document }}</td>
                                <td class="px-6 py-4">{{ $driver->telefone ?? 'N/A' }}</td>
                                <td class="px-6 py-4 capitalize"><span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $driver->type }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->is_authorized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $driver->is_authorized ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2 text-center whitespace-nowrap">
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
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum motorista
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cards Mobile/Tablet --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($drivers as $driver)
                    <div
                        class="bg-white border rounded-lg p-4 shadow-sm flex flex-col justify-between {{ $driver->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="font-semibold text-lg truncate" title="{{ $driver->name }}">
                                    {{ $driver->name }}</h3>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->is_authorized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $driver->is_authorized ? 'Autorizado' : 'Não Autorizado' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600"><strong>Documento:</strong> {{ $driver->document }}</p>
                            <p class="text-sm text-gray-600"><strong>Telefone:</strong>
                                {{ $driver->telefone ?? 'Não informado' }}</p>
                            <p class="text-sm text-gray-600"><strong>Tipo:</strong> {{ $driver->type }}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-2 justify-end">
                            @if ($driver->trashed())
                                <x-secondary-button class="flex-grow"
                                    wire:click="restore({{ $driver->id }})">Restaurar</x-secondary-button>
                                <x-danger-button class="flex-grow"
                                    wire:click="confirmForceDelete({{ $driver->id }})">Excluir
                                    Perm.</x-danger-button>
                            @else
                                <x-primary-button class="flex-grow"
                                    wire:click="showHistory({{ $driver->id }})">Histórico</x-primary-button>
                                <x-secondary-button class="flex-grow"
                                    wire:click="edit({{ $driver->id }})">Editar</x-secondary-button>
                                <x-danger-button class="flex-grow"
                                    wire:click="confirmDelete({{ $driver->id }})">Excluir</x-danger-button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-1 md:col-span-2">Nenhum motorista cadastrado.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>

    {{-- Modal de Edição/Criação --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            x-data="{ open: @entangle('isModalOpen') }" x-show="open" @keydown.escape.window="$wire.closeModal()"
            style="display: none;">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.closeModal()">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">
                        {{ $driverId ? 'Editar Motorista' : 'Cadastrar Novo Motorista' }}</h3>
                </div>
                <form wire:submit="store">
                    <div class="p-6 space-y-4" x-data="{ driverType: @entangle('type').live }">
                        <div>
                            <x-input-label for="name" value="Nome Completo" />
                            <x-text-input type="text" id="name" class="mt-1 block w-full capitalize"
                                wire:model="name" maxlength="100" />
                            <x-input-error for="name" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="document" value="Documento (CPF)" />
                            <x-text-input type="text" id="document" class="mt-1 block w-full"
                                wire:model="document" x-data x-mask="999.999.999-99" />
                            <x-input-error for="document" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="telefone" value="Telefone (Opcional)" />
                            <x-text-input type="text" id="telefone" class="mt-1 block w-full"
                                wire:model="telefone" x-data x-mask="(99) 99999-9999" />
                            <x-input-error for="telefone" class="mt-1" />
                        </div>
                        {{-- Campo Tipo --}}
                        <div>
                            <x-input-label for="type" value="Tipo" />
                            <select id="type"
                                class="mt-1 block w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm"
                                {{-- Usa wire:model.live para atualizar o Alpine.js --}} wire:model.live="type">
                                <option value="Servidor">Servidor</option>
                                <option value="Aluno">Aluno</option>
                                <option value="Terceirizado">Terceirizado</option>
                                <option value="Visitante">Visitante</option>
                            </select>
                            <x-input-error for="type" class="mt-1" />
                        </div>
                        {{-- Campo Autorizado (com lógica condicional) --}}
                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'fiscal')
                            {{-- Usa x-show para esconder OU x-bind:disabled para desabilitar --}}
                            {{-- Opção 1: Esconder a checkbox --}}
                            {{-- <div class="flex items-center pt-2" x-show="driverType === 'Servidor' || driverType === 'Terceirizado'"> --}}

                            {{-- Opção 2: Desabilitar a checkbox (recomendado) --}}
                            <div class="flex items-center pt-2" x-data="{ isDisabled: driverType === 'Aluno' || driverType === 'Visitante' }" x-init="$watch('driverType', value => isDisabled = (value === 'Aluno' || value === 'Visitante'))">
                                <input id="is_authorized" type="checkbox"
                                    class="h-4 w-4 text-ifnmg-green border-gray-300 rounded focus:ring-ifnmg-green disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:model="is_authorized" {{-- Desabilita E desmarca se o tipo for inválido --}} x-bind:disabled="isDisabled"
                                    x-bind:checked="!isDisabled && $wire.is_authorized">
                                <label for="is_authorized" class="ml-2 block text-sm text-gray-900"
                                    :class="{ 'text-gray-500': isDisabled }">
                                    Autorizado a dirigir frota oficial?
                                </label>
                                {{-- Mostra erro de validação do backend se houver --}}
                                <x-input-error for="is_authorized" class="mt-1 ml-6 text-xs" />
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md"
                @click.away="$wire.set('isConfirmModalOpen', false)">
                <div class="p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">
                                {{ $filter === 'active' ? 'Mover para a Lixeira' : 'Confirmar Exclusão Permanente' }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    Você tem certeza que deseja
                                    <strong>{{ $filter === 'active' ? 'mover o motorista' : 'excluir PERMANENTEMENTE o motorista' }}</strong>
                                    <strong>"{{ $driverNameToDelete }}"</strong>?
                                </p>
                                @if ($filter === 'trashed')
                                    <p class="mt-2 text-xs text-red-700 font-semibold">Esta ação não pode ser desfeita.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex flex-row-reverse space-x-2 space-x-reverse">
                    <x-danger-button
                        wire:click="{{ $filter === 'active' ? 'deleteDriver' : 'forceDeleteDriver' }}">Confirmar</x-danger-button>
                    <x-secondary-button wire:click="closeConfirmModal">Cancelar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Histórico --}}
    @if ($isHistoryModalOpen && $driverForHistory)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">

                {{-- CABEÇALHO DO MODAL ATUALIZADO --}}
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Histórico de Movimentação</h3>
                    <p class="text-sm text-gray-600">{{ $driverForHistory->name }}</p>

                    {{-- CAMPO DE BUSCA ADICIONADO --}}
                    <div class="relative mt-4">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="historySearch" type="text"
                            placeholder="Buscar por veículo, destino, motivo ou data..."
                            class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:border-ifnmg-green focus:ring-ifnmg-green">
                    </div>
                </div>

                <div class="p-6 overflow-y-auto bg-gray-50">
                    @if ($driverHistory->isNotEmpty())
                        <div class="relative pl-6">
                            {{-- A linha do tempo vertical --}}
                            <div class="absolute left-9 top-0 h-full w-0.5 bg-gray-200"></div>

                            @foreach ($driverHistory as $entry)
                                <div class="relative mb-8">
                                    {{-- O ponto na linha do tempo com ícone --}}
                                    <div class="absolute left-0 top-1.5 -translate-x-1/2 transform">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full {{ $entry['type'] === 'Oficial' ? 'bg-blue-500' : 'bg-green-500' }}">
                                            @if ($entry['type'] === 'Oficial')
                                                {{-- Ícone de Caminhão --}}
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 003.375-3.375h1.5a1.125 1.125 0 011.125 1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375m15.75 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125-1.125h-1.5a3.375 3.375 0 00-3.375 3.375v1.875" />
                                                </svg>
                                            @else
                                                {{-- Ícone de Carro --}}
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 9.75h7.5a.75.75 0 01.75.75v3.75a.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75v-3.75a.75.75 0 01.75-.75z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ml-12 p-4 bg-white border rounded-lg shadow-sm">
                                        <div class="flex justify-between items-center">
                                            <p class="font-semibold text-gray-800">Viagem {{ $entry['type'] }}</p>
                                            <span
                                                class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 space-y-1">
                                            <p><strong>Veículo:</strong> {{ $entry['vehicle_info'] }}</p>
                                            <p><strong>Período:</strong>
                                                {{ \Carbon\Carbon::parse($entry['start_time'])->format('H:i') }}
                                                @if ($entry['end_time'])
                                                    até {{ \Carbon\Carbon::parse($entry['end_time'])->format('H:i') }}
                                                @else
                                                    (em trânsito)
                                                @endif
                                            </p>
                                            <p><strong>{{ $entry['type'] === 'Oficial' ? 'Destino:' : 'Motivo:' }}</strong>
                                                {{ $entry['detail'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500">Nenhum histórico de movimentação para este condutor.</p>
                    @endif

                    @if ($driverHistory)
                        <div class="mt-4">
                            {{ $driverHistory->links() }}
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-white text-right mt-auto border-t">
                    <x-secondary-button wire:click="closeHistoryModal">Fechar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif
</div>
