<div>
    {{-- Seção de Mensagens de Alerta --}}
    @if ($successMessage)
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
                    <p class="text-sm">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->role !== 'fiscal')
        <div class="space-y-8">

            {{-- Card de Registro de ENTRADA --}}
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Registrar Nova Entrada de Veículo</h2>
                    <p class="text-sm text-gray-500 mt-1">Use a busca rápida ou preencha os campos manualmente.</p>
                </div>

                <form wire:submit="save" class="p-6">
                    <div class="space-y-6">
                        {{-- Campo de Busca Inteligente de Veículos --}}
                        <div class="relative">
                            <label for="search" class="block text-sm font-medium text-gray-700">Busca Rápida (Veículo
                                ou Motorista)</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="search" wire:model.live.debounce.300ms="search"
                                    placeholder="Digite a placa, modelo ou nome para preencher"
                                    class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                            </div>

                            @if (strlen($search) >= 3)
                                <div
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    @if (!empty($searchResults) && $searchResults->isNotEmpty())
                                        <ul>
                                            @foreach ($searchResults as $result)
                                                <li class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm"
                                                    wire:click="selectVehicle({{ $result['id'] }})">
                                                    {{ $result['text'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="px-4 py-3 text-sm text-gray-500">Nenhum resultado. Preencha os campos
                                            para um novo cadastro.</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Campos do Formulário do Veículo --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                            <div x-data="{
                                plate: $wire.entangle('license_plate').live,
                                formatPlate() {
                                    let clean = String(this.plate || '').toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 7);
                                    let formatted = '';
                                    for (let i = 0; i < clean.length; i++) {
                                        const char = clean[i];
                                        if (i <= 2 && /[A-Z]/.test(char)) formatted += char;
                                        else if (i === 3 && /[0-9]/.test(char)) formatted += char;
                                        else if (i === 4 && /[A-Z0-9]/.test(char)) formatted += char;
                                        else if (i >= 5 && /[0-9]/.test(char)) formatted += char;
                                    }
                                    if (/^[A-Z]{3}[0-9]{4}$/.test(formatted)) {
                                        formatted = formatted.substring(0, 3) + '-' + formatted.substring(3);
                                    }
                                    this.plate = formatted;
                                }
                            }" x-init="$watch('plate', () => formatPlate())">
                                <label for="license_plate" class="block text-sm font-medium text-gray-700">Placa</label>
                                <input type="text" id="license_plate" x-model="plate" placeholder="Digite a placa..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green @error('license_plate') border-red-500 @enderror">
                                @error('license_plate')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Modelo do
                                    Veículo</label>
                                <input type="text" id="vehicle_model" wire:model.live="vehicle_model"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green @error('vehicle_model') border-red-500 @enderror">
                                @error('vehicle_model')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- SEÇÃO REFINADA DO MOTORISTA --}}
                        <div>
                            <x-input-label for="driver_search" value="Motorista" />

                            @if ($showNewVisitorForm)
                                {{-- MODO CADASTRO DE NOVO VISITANTE --}}
                                <div
                                    class="p-4 border border-blue-300 bg-blue-50 rounded-lg mt-2 space-y-4 animate-fade-in-down">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-blue-800">Cadastrar Novo Visitante</h3>
                                        <button type="button" wire:click="cancelNewVisitor"
                                            class="text-sm text-gray-600 hover:text-gray-900 transition">Cancelar</button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        {{-- Campo Nome --}}
                                        <div class="md:col-span-1">
                                            <x-input-label for="new_visitor_name" value="Nome Completo" />
                                            <x-text-input wire:model.live="new_visitor_name" id="new_visitor_name"
                                                class="block mt-1 w-full" type="text" />
                                            <x-input-error for="new_visitor_name" class="mt-1" />
                                        </div>

                                        {{-- Campo CPF --}}
                                        <div>
                                            <x-input-label for="new_visitor_document" value="CPF" />
                                            <x-text-input wire:model.live="new_visitor_document"
                                                id="new_visitor_document" class="block mt-1 w-full" type="text"
                                                x-mask="999.999.999-99" />
                                            <x-input-error for="new_visitor_document" class="mt-1" />
                                        </div>

                                        {{-- Campo Telefone (Opcional) --}}
                                        <div>
                                            <x-input-label for="new_visitor_phone" value="Telefone (Opcional)" />
                                            <x-text-input wire:model="new_visitor_phone" id="new_visitor_phone"
                                                class="block mt-1 w-full" type="text" x-mask="(99) 99999-9999" />
                                            <x-input-error for="new_visitor_phone" class="mt-1" />
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- MODO BUSCA --}}
                                <div class="relative">
                                    <x-text-input wire:model.live.debounce.300ms="driver_search" id="driver_search"
                                        class="block mt-1 w-full" placeholder="Digite para buscar..."
                                        :disabled="$selected_driver_id" autocomplete="off" />

                                    @if (!empty($driver_search) && !$selected_driver_id)
                                        <div
                                            class="absolute z-20 w-full bg-white rounded-md shadow-lg mt-1 border border-gray-200">
                                            @if (count($drivers) > 0)
                                                <ul class="max-h-60 overflow-y-auto">
                                                    @foreach ($drivers as $driver)
                                                        <li class="px-4 py-3 hover:bg-gray-100 cursor-pointer text-sm"
                                                            wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')">
                                                            {{ $driver->name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="p-4 text-center text-sm text-gray-500">
                                                    Nenhum motorista encontrado.
                                                    <button type="button" wire:click="prepareNewVisitorForm"
                                                        class="text-ifnmg-green hover:underline font-semibold ml-1">
                                                        Cadastrar "{{ $driver_search }}"?
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <x-input-error for="selected_driver_id" class="mt-1" />
                            @endif
                        </div>

                        {{-- Seção do Motivo da Entrada --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="entry_reason" class="block text-sm font-medium text-gray-700">Motivo da
                                    Entrada</label>
                                <select id="entry_reason" wire:model.live="entry_reason"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green @error('entry_reason') border-red-500 @enderror">
                                    <option value="">Selecione um motivo</option>
                                    @foreach ($predefinedReasons as $reason)
                                        <option value="{{ $reason }}">{{ $reason }}</option>
                                    @endforeach
                                    <option value="Outro">Outro</option>
                                </select>
                                @error('entry_reason')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @if ($entry_reason === 'Outro')
                                <div>
                                    <label for="other_reason"
                                        class="block text-sm font-medium text-gray-700">Especifique o Motivo</label>
                                    <input type="text" id="other_reason" wire:model="other_reason"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green @error('other_reason') border-red-500 @enderror">
                                    @error('other_reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end pt-6 border-t">
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-ifnmg-green px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="save"
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Salvar Entrada</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Card de Registro de SAÍDA --}}
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Registrar Saída (Veículos no Pátio)</h2>
                    <p class="text-sm text-gray-500 mt-1">Localize um veículo para registrar sua saída.</p>
                </div>

                <div class="p-6">
                    <div class="relative mb-4">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="exitSearch"
                            placeholder="Buscar por placa ou motorista no pátio..."
                            class="block w-full md:w-1/3 rounded-md border-gray-300 pl-10 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                    </div>

                    {{-- Tabela para Desktop --}}
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Placa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Modelo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Motorista</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Entrada</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($currentVehicles as $entry)
                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-700">
                                            {{ $entry->license_plate }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $entry->vehicle_model ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $entry->driver->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $entry->entry_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button wire:click="confirmExit({{ $entry->id }})"
                                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                Registrar Saída
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum veículo
                                            no pátio.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Cards para Mobile/Tablet --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                        @forelse ($currentVehicles as $entry)
                            <div class="bg-gray-50 border rounded-lg p-4 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-semibold text-lg font-mono">{{ $entry->license_plate }}</h3>
                                        <span
                                            class="text-xs text-gray-500">{{ $entry->entry_at->format('d/m H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        {{ $entry->vehicle_model ?? 'Modelo não informado' }}</p>
                                    <p class="text-sm text-gray-600 mt-1"><span
                                            class="font-semibold">Motorista:</span>
                                        {{ $entry->driver->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mt-4">
                                    <button wire:click="confirmExit({{ $entry->id }})"
                                        class="w-full inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                        Registrar Saída
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 col-span-1 md:col-span-2">Nenhum veículo no pátio.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    @else
        {{-- Mensagem para o fiscal --}}
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">Você tem permissão apenas para visualização. O registro de entradas e saídas
                é restrito aos porteiros e administradores.</span>
        </div>
    @endif

    {{-- Modal de confirmação --}}
    @if ($showExitConfirmationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md"
                @click.away="$wire.set('showExitConfirmationModal', false)">
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
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">Confirmar Saída de Veículo</h3>
                            <div class="mt-2">
                                @if ($entryToExit)
                                    <p class="text-sm text-gray-600">
                                        Tem certeza de que deseja registrar a saída para o seguinte veículo?
                                    </p>
                                    <div class="mt-4 text-sm bg-gray-50 p-3 rounded-lg border">
                                        <p><strong>Placa:</strong> <span
                                                class="font-mono">{{ $entryToExit->license_plate }}</span></p>
                                        <p><strong>Modelo:</strong> {{ $entryToExit->vehicle_model }}</p>
                                        <p><strong>Condutor:</strong>
                                            {{ $entryToExit->driver->name ?? 'Não informado' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex flex-row-reverse space-x-2 space-x-reverse">
                    <x-danger-button wire:click="executeExit">Confirmar Saída</x-danger-button>
                    <x-secondary-button
                        wire:click="$set('showExitConfirmationModal', false)">Cancelar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif
</div>
