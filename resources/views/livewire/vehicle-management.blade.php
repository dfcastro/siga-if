<div>
    {{-- Alerta de Sucesso --}}
    @if (session('successMessage'))
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
                    <p class="text-sm">{{ session('successMessage') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Gerenciamento de Veículos</h2>
                <p class="text-sm text-gray-500 mt-1">Adicione, edite e visualize todos os veículos cadastrados.</p>
            </div>
            @if (in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal']))
                <div class="mt-4 sm:mt-0">
                    <x-primary-button wire:click="create">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Novo Veículo
                    </x-primary-button>
                </div>
            @endif
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
                    <input wire:model.live.debounce.500ms="search" type="text"
                        placeholder="Buscar por placa, modelo..."
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proprietário
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($vehicles as $vehicle)
                            <tr
                                class="{{ $vehicle->trashed() ? 'bg-red-50' : 'odd:bg-white even:bg-gray-50' }} hover:bg-blue-50">
                                <td class="px-6 py-4 font-mono">{{ $vehicle->license_plate }}</td>
                                <td class="px-6 py-4">{{ $vehicle->model }}</td>
                                <td class="px-6 py-4"><span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vehicle->type == 'Oficial' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ $vehicle->type }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $vehicle->driver->name ?? 'Sem proprietário' }}</td>
                                <td class="px-6 py-4 space-x-2 text-center whitespace-nowrap">
                                    @if ($vehicle->trashed())
                                        <button wire:click="restore({{ $vehicle->id }})"
                                            class="font-medium text-green-600 hover:text-green-800">Restaurar</button>
                                        <button wire:click="confirmForceDelete({{ $vehicle->id }})"
                                            class="font-medium text-red-600 hover:text-red-800 ml-2">Excluir
                                            Perm.</button>
                                    @else
                                        <button wire:click="showHistory({{ $vehicle->id }})"
                                            class="font-medium text-blue-600 hover:text-blue-800">Histórico</button>
                                        @if (auth()->user()->role === 'admin' ||
                                                ($vehicle->type === 'Oficial' && auth()->user()->role === 'fiscal') ||
                                                ($vehicle->type === 'Particular' && auth()->user()->role === 'porteiro'))
                                            <x-secondary-button wire:click="edit({{ $vehicle->id }})"
                                                class="ml-2">Editar</x-secondary-button>

                                            <x-danger-button
                                                wire:click="confirmDelete({{ $vehicle->id }})">Excluir</x-danger-button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum veículo
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cards Mobile/Tablet --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($vehicles as $vehicle)
                    <div
                        class="bg-white border rounded-lg p-4 shadow-sm flex flex-col justify-between {{ $vehicle->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                        <div>
                            <div class="flex justify-between items-start">
                                <h3 class="font-semibold text-lg font-mono">{{ $vehicle->license_plate }}</h3>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vehicle->type == 'Oficial' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">{{ $vehicle->type }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $vehicle->model }}</p>
                            <p class="text-sm text-gray-600 mt-2"><strong>Proprietário:</strong>
                                {{ $vehicle->driver->name ?? 'Sem proprietário' }}</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-2 justify-end">
                            @if ($vehicle->trashed())
                                <x-secondary-button class="flex-grow"
                                    wire:click="restore({{ $vehicle->id }})">Restaurar</x-secondary-button>
                                <x-danger-button class="flex-grow"
                                    wire:click="confirmForceDelete({{ $vehicle->id }})">Excluir
                                    Perm.</x-danger-button>
                            @else
                                <x-primary-button class="flex-grow"
                                    wire:click="showHistory({{ $vehicle->id }})">Histórico</x-primary-button>
                                @if (auth()->user()->role === 'admin' ||
                                        ($vehicle->type === 'Oficial' && auth()->user()->role === 'fiscal') ||
                                        ($vehicle->type === 'Particular' && auth()->user()->role === 'porteiro'))
                                    {{-- Botão Editar --}}
                                    <x-secondary-button class="flex-grow" wire:click="edit({{ $vehicle->id }})">
                                        Editar
                                    </x-secondary-button>
                                    {{-- Botão Excluir (agora dentro da mesma condição) --}}
                                    <x-danger-button class="flex-grow" wire:click="confirmDelete({{ $vehicle->id }})">
                                        Excluir
                                    </x-danger-button>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-1 md:col-span-2">Nenhum veículo cadastrado.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

    {{-- Modal de Edição/Criação --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            x-data="{ open: @entangle('isModalOpen') }" x-show="open" @keydown.escape.window="open = false" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="open = false">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">{{ $vehicleId ? 'Editar Veículo' : 'Criar Novo Veículo' }}</h3>
                </div>
                <form wire:submit="store">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="license_plate_modal" value="Placa" />
                                <x-text-input type="text" id="license_plate_modal" wire:model.blur="license_plate"
                                    class="mt-1 block w-full uppercase font-mono" />
                                <x-input-error for="license_plate" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="model" value="Modelo" />
                                <x-text-input type="text" id="model" wire:model="model" maxlength="25"
                                    oninput="this.value = this.value.toUpperCase()" class="mt-1 block w-full" />
                                <x-input-error for="model" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="color_selector" value="Cor" />
                                <select id="color_selector" wire:model="color"
                                    class="mt-1 block w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm">
                                    <option value="">Selecione uma cor...</option>
                                    @foreach ($commonColors as $colorOption)
                                        <option value="{{ $colorOption }}">{{ $colorOption }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="color" class="mt-1" />
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="type" value="Tipo" />
                                <select id="type" wire:model.live="type"
                                    class="mt-1 block w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm">
                                    @if (in_array(auth()->user()->role, ['admin', 'fiscal']))
                                        <option value="Oficial">Oficial</option>
                                    @endif
                                    <option value="Particular">Particular</option>
                                </select>
                                <x-input-error for="type" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="driver_search" value="Motorista (opcional)" />
                                <div class="relative" x-data="{ open: false }"
                                    x-on:close-driver-dropdown.window="open = false" @click.away="open = false"
                                    @keydown.escape.window="open = false">
                                    <input id="driver_search" type="text"
                                        class="block mt-1 w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm"
                                        wire:model.live.debounce.300ms="driver_search" @click="open = true"
                                        @input="open = true" placeholder="Digite para buscar..." autocomplete="off">
                                    <div x-show="open" x-transition
                                        class="absolute z-50 w-full bg-white rounded-md shadow-lg mt-1 max-h-40 overflow-y-auto border">
                                        @if ($this->foundDrivers->isNotEmpty())
                                            <ul>
                                                @foreach ($this->foundDrivers as $driver)
                                                    <li class="px-4 py-2 cursor-pointer hover:bg-gray-200"
                                                        wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')">
                                                        {{ $driver->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif(strlen($driver_search) > 1)
                                            <div class="px-4 py-2 text-gray-500">Nenhum motorista encontrado.</div>
                                        @endif
                                    </div>
                                </div>
                                <x-input-error for="driver_id" class="mt-1" />
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                        <x-secondary-button type="button" @click="open = false">Fechar</x-secondary-button>
                        <x-primary-button type="submit">{{ $vehicleId ? 'Atualizar' : 'Salvar' }}</x-primary-button>
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
                                    <strong>{{ $filter === 'active' ? 'mover o veículo' : 'excluir PERMANENTEMENTE o veículo' }}</strong>
                                    <strong class="font-mono">"{{ $vehiclePlateToDelete }}"</strong>?
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
                        wire:click="{{ $filter === 'active' ? 'deleteVehicle' : 'forceDeleteVehicle' }}">Confirmar</x-danger-button>
                    <x-secondary-button wire:click="closeConfirmModal">Cancelar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Histórico com Busca e Timeline --}}
    @if ($isHistoryModalOpen && $vehicleForHistory)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Histórico de Movimentação</h3>
                    <p class="text-sm text-gray-600">{{ $vehicleForHistory->model }} - <span
                            class="font-mono">{{ $vehicleForHistory->license_plate }}</span></p>

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
                            placeholder="Buscar por condutor, destino, motivo ou data..."
                            class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:border-ifnmg-green focus:ring-ifnmg-green">
                    </div>
                </div>

                <div class="p-6 overflow-y-auto bg-gray-50">
                    @if ($vehicleHistory && $vehicleHistory->isNotEmpty())
                        <div class="relative pl-6">
                            <div class="absolute left-9 top-0 h-full w-0.5 bg-gray-200"></div>
                            @foreach ($vehicleHistory as $entry)
                                <div class="relative mb-8">
                                    <div class="absolute left-0 top-1.5 -translate-x-1/2 transform">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full {{ $entry['type'] === 'Oficial' ? 'bg-blue-500' : 'bg-green-500' }}">
                                            @if ($entry['type'] === 'Oficial')
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 003.375-3.375h1.5a1.125 1.125 0 011.125 1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375m15.75 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125-1.125h-1.5a3.375 3.375 0 00-3.375 3.375v1.875" />
                                                </svg>
                                            @else
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
                                            <p><strong>Condutor:</strong> {{ $entry['driver_name'] }}</p>
                                            <p><strong>Período:</strong>
                                                {{ \Carbon\Carbon::parse($entry['start_time'])->format('H:i') }}
                                                @if ($entry['end_time'])
                                                    até {{ \Carbon\Carbon::parse($entry['end_time'])->format('H:i') }}
                                                @else
                                                    (em andamento)
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
                        <p class="text-center text-gray-500 pt-4">Nenhum histórico de movimentação encontrado para este
                            veículo.</p>
                    @endif
                    @if ($vehicleHistory)
                        <div class="mt-4">
                            {{ $vehicleHistory->links() }}
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
