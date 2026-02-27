<div x-data="{ tab: 'andamento' }">
    {{-- Mensagens de Alerta --}}
    @if (session()->has('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4 shadow-sm"
            role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Sucesso!</p>
                    <p class="text-xs">{{ session('successMessage') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->role !== 'fiscal')

        {{-- Cabeçalho e Botão Principal --}}
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Diário de Bordo Oficial</h2>
                <p class="text-sm text-gray-500">Gerencie a saída e chegada da frota da instituição.</p>
            </div>
            <button wire:click="create"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-md bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                REGISTRAR NOVA SAÍDA
            </button>
        </div>

        {{-- NAVEGAÇÃO DAS ABAS (TABS) --}}
        <div class="bg-white shadow-sm sm:rounded-t-lg border-b border-gray-200 mb-4">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                <button @click="tab = 'andamento'"
                    :class="tab === 'andamento' ? 'border-blue-600 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm sm:text-base transition-colors duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                    🚗 Em Andamento
                    @if (count($ongoingTrips) > 0)
                        <span
                            class="bg-blue-100 text-blue-800 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ count($ongoingTrips) }}</span>
                    @endif
                </button>
                <button @click="tab = 'concluidas'"
                    :class="tab === 'concluidas' ? 'border-blue-600 text-blue-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm sm:text-base transition-colors duration-200 whitespace-nowrap">
                    ✅ Viagens Concluídas
                </button>
            </nav>
        </div>

        <div>
            {{-- ========================================================= --}}
            {{-- ABA 1: VIAGENS EM ANDAMENTO --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'andamento'" x-transition.opacity.duration.300ms
                class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg sm:rounded-t-none rounded-lg border border-gray-100 p-4 sm:p-6">

                {{-- Tabela para Desktop --}}
                <div class="hidden md:block overflow-x-auto border border-gray-200 sm:rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Veículo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Condutor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Destino</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Saída / KM</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ongoingTrips as $trip)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-6 py-4 align-middle">
                                        <div class="text-sm font-bold text-gray-900">{{ $trip->vehicle->model }}</div>
                                        <div class="text-sm font-mono text-gray-500">{{ $trip->vehicle->license_plate }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-700 font-medium">
                                        {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        {{ $trip->destination }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div class="font-medium">
                                            {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs text-blue-600 mt-0.5 font-semibold">
                                            {{ number_format($trip->departure_odometer, 0, ',', '.') }} km</div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-center">
                                        <button wire:click="openArrivalModal({{ $trip->id }})"
                                            class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-sm font-bold text-green-600 border border-green-200 shadow-sm hover:bg-green-50 hover:border-green-300 transition">
                                            Registrar Chegada
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        Nenhuma viagem oficial em andamento no momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards para Mobile/Tablet --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden">
                    @forelse ($ongoingTrips as $trip)
                        <div
                            class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col justify-between relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                            <div class="pl-2">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900 leading-none">
                                            {{ $trip->vehicle->model }}</h3>
                                        <p class="text-sm font-mono text-gray-500 mt-1">
                                            {{ $trip->vehicle->license_plate }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-2 py-1 rounded-md border border-blue-100">
                                            {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('H:i') }}h
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-2 text-sm text-gray-700">
                                    <p class="bg-gray-50 p-2 rounded-md"><span
                                            class="font-semibold text-gray-500 text-xs uppercase">Condutor:</span><br>
                                        {{ $trip->driver ? $trip->driver->name : 'N/D' }}</p>
                                    <p><span class="font-semibold text-gray-500 text-xs uppercase">Destino:</span><br>
                                        {{ $trip->destination }}</p>
                                    <p><span class="font-semibold text-gray-500 text-xs uppercase">KM Saída:</span><br>
                                        <span
                                            class="font-mono text-blue-600 font-bold">{{ number_format($trip->departure_odometer, 0, ',', '.') }}
                                            km</span>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 pl-2">
                                <button wire:click="openArrivalModal({{ $trip->id }})"
                                    class="w-full flex justify-center items-center gap-2 rounded-md bg-green-50 border border-green-200 px-4 py-2.5 text-sm font-bold text-green-700 shadow-sm hover:bg-green-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Registrar Chegada
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-1 sm:col-span-2 text-center py-10 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                            <p class="text-gray-500">Nenhuma viagem em andamento.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- ABA 2: VIAGENS CONCLUÍDAS --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'concluidas'" style="display: none;"
                class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg sm:rounded-t-none rounded-lg border border-gray-100 p-4 sm:p-6">

                <div class="mb-4">
                    <div class="relative w-full md:w-1/2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por destino, veículo ou motorista..."
                            class="block w-full border-gray-300 rounded-md pl-10 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                {{-- Tabela para Desktop --}}
                <div class="hidden md:block overflow-x-auto border border-gray-200 sm:rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Veículo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Condutor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Período / Destino</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Distância</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($completedTrips as $trip)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 align-middle">
                                        <div class="text-sm font-bold text-gray-900">{{ $trip->vehicle->model }}</div>
                                        <div class="text-xs font-mono text-gray-500">
                                            {{ $trip->vehicle->license_plate }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-700">
                                        {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-xs font-semibold text-gray-800">{{ $trip->destination }}</span>
                                            <span class="text-xs text-gray-500">
                                                🟢 S:
                                                {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/y H:i') }}<br>
                                                🔴 C:
                                                {{ $trip->arrival_datetime ? \Carbon\Carbon::parse($trip->arrival_datetime)->format('d/m/y H:i') : '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm">
                                        <span class="bg-gray-100 text-gray-800 font-bold px-2 py-1 rounded">
                                            {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                            km
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">Nenhuma viagem
                                        concluída encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- O NOVO CARD MOBILE PARA VIAGENS CONCLUÍDAS --}}
                <div class="grid grid-cols-1 gap-4 md:hidden">
                    @forelse ($completedTrips as $trip)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-400"></div>
                            <div class="pl-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-gray-900">{{ $trip->vehicle->model }} <span
                                            class="font-mono text-xs text-gray-500 ml-1">{{ $trip->vehicle->license_plate }}</span>
                                    </div>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded border border-gray-200">
                                        {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                        km
                                    </span>
                                </div>
                                <div class="text-sm text-gray-700 mb-2">
                                    <span class="font-semibold text-xs text-gray-500 uppercase block">Condutor:</span>
                                    {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                </div>
                                <div class="bg-gray-50 p-2 rounded-md text-xs text-gray-600 space-y-1">
                                    <p class="font-semibold text-gray-800 mb-1">{{ $trip->destination }}</p>
                                    <p>🟢 Saída:
                                        {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/Y H:i') }}</p>
                                    <p>🔴 Chegada:
                                        {{ $trip->arrival_datetime ? \Carbon\Carbon::parse($trip->arrival_datetime)->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                            <p class="text-gray-500">Nenhuma viagem concluída encontrada.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $completedTrips->links() }}
                </div>
            </div>
        </div>
    @else
        {{-- Mensagem para o fiscal --}}
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">Você não tem permissão para acessar esta página. O registro de saídas e
                entradas de veículos oficiais é restrito aos porteiros e administradores.</span>
        </div>
    @endif


    {{-- ================================================================= --}}
    {{-- MODAL DE REGISTRO DE SAÍDA --}}
    {{-- ================================================================= --}}
    <x-modal wire:model.live="isDepartureModalOpen" maxWidth="4xl">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Registrar Saída de Veículo Oficial</h3>
        </div>
        <form wire:submit="storeDeparture" novalidate>
            <div class="p-6" x-data="{
                formatNumber(value) {
                    if (!value) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    let limited = clean.substring(0, 7);
                    return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                    {{-- Veículo --}}
                    <div x-data="{ open: @entangle('show_vehicle_dropdown') }" @click.away="open = false" class="relative">
                        <x-input-label for="vehicle_search" :value="__('Veículo')" />
                        <x-text-input type="text" id="vehicle_search"
                            class="mt-1 block w-full text-sm sm:text-base"
                            wire:model.live.debounce.300ms="vehicle_search" @focus="open = true"
                            placeholder="Placa ou modelo..." autocomplete="off" />

                        <div x-show="open" x-transition
                            class="absolute z-30 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @if (is_iterable($vehicle_results) && count($vehicle_results) > 0)
                                    @foreach ($vehicle_results as $vehicle)
                                        <li wire:click="selectVehicle({{ $vehicle->id }}, '{{ $vehicle->model }} ({{ $vehicle->license_plate }})')"
                                            class="px-4 py-3 cursor-pointer hover:bg-blue-50 text-sm border-b border-gray-50 last:border-0 transition-colors">
                                            <span class="font-bold text-gray-800">{{ $vehicle->model }}</span>
                                            <span
                                                class="font-mono text-xs text-gray-500 ml-1">{{ $vehicle->license_plate }}</span>
                                        </li>
                                    @endforeach
                                @elseif (strlen($vehicle_search) >= 2)
                                    <li class="px-4 py-3 text-sm text-gray-500">Nenhum veículo oficial encontrado.</li>
                                @endif
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                    </div>

                    {{-- Motorista com Exibição de CPF --}}
                    <div x-data="{ open: @entangle('show_driver_dropdown') }" @click.away="open = false" class="relative">
                        <x-input-label for="driver_search" :value="__('Motorista / Condutor')" />
                        <x-text-input type="text" id="driver_search"
                            class="mt-1 block w-full text-sm sm:text-base"
                            wire:model.live.debounce.300ms="driver_search" @focus="open = true"
                            placeholder="Nome ou CPF..." autocomplete="off" />

                        <div x-show="open" x-transition
                            class="absolute z-30 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul class="divide-y divide-gray-100">
                                @if (is_iterable($driver_results) && count($driver_results) > 0)
                                    @foreach ($driver_results as $driver)
                                        <li wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')"
                                            class="px-4 py-3 cursor-pointer hover:bg-blue-50 transition-colors">
                                            <div class="font-medium text-sm text-gray-800">{{ $driver->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                CPF: {{ $driver->formatted_document }}
                                            </div>
                                        </li>
                                    @endforeach
                                @elseif (strlen($driver_search) >= 2)
                                    <li class="px-4 py-3 text-sm text-gray-500 text-center">Nenhum motorista
                                        encontrado.</li>
                                @endif
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-1" />
                    </div>

                    {{-- Destino --}}
                    <div>
                        <x-input-label for="destination" :value="__('Destino')" />
                        <x-text-input type="text" id="destination" class="mt-1 block w-full text-sm sm:text-base"
                            wire:model="destination" maxlength="255"
                            placeholder="Ex: Campus Salinas, Prefeitura..." />
                        <x-input-error :messages="$errors->get('destination')" class="mt-1" />
                    </div>

                    {{-- Odômetro --}}
                    <div>
                        <x-input-label for="departure_odometer" :value="__('Quilometragem de Saída (km)')" />
                        <x-text-input type="text" id="departure_odometer"
                            class="mt-1 block w-full font-mono text-sm sm:text-base"
                            x-on:input="$event.target.value = formatNumber($event.target.value)"
                            wire:model="departure_odometer" placeholder="Ex: 45.120" />
                        @if ($lastOdometer !== null)
                            <p class="text-xs text-blue-600 mt-1.5 font-medium flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Última KM registrada: {{ number_format($lastOdometer, 0, ',', '.') }} km
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('departure_odometer')" class="mt-1" />
                    </div>
                </div>

                {{-- Campos Opcionais --}}
                <div class="mt-4 sm:mt-6 border-t border-gray-100 pt-4">
                    <x-input-label for="passengers" :value="__('Passageiros (Opcional)')" />
                    <textarea id="passengers"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                        wire:model="passengers" rows="2" maxlength="1000"
                        placeholder="Nome dos servidores/alunos transportados..."></textarea>
                </div>
                <div class="mt-4">
                    <x-input-label for="return_observation" :value="__('Previsão de Retorno / Observação (Opcional)')" />
                    <textarea id="return_observation"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base"
                        wire:model="return_observation" rows="2" maxlength="1000"
                        placeholder="Ex: Previsão de retorno na quinta-feira à tarde."></textarea>
                </div>
            </div>

            {{-- Botões do Modal Mobile Friendly --}}
            <div
                class="px-4 sm:px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 border-t">
                <button type="button" wire:click="closeDepartureModal"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    SALVAR SAÍDA
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ================================================================= --}}
    {{-- MODAL DE REGISTRO DE CHEGADA --}}
    {{-- ================================================================= --}}
    <x-modal wire:model.live="isArrivalModalOpen" maxWidth="md">
        @if ($tripToUpdate)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Registrar Chegada Oficial</h3>
            </div>
            <form wire:submit="storeArrival" x-data="{
                formatNumber(value) {
                    if (!value) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    let limited = clean.substring(0, 7);
                    return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <div class="p-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 space-y-2 text-sm text-gray-700">
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="font-semibold text-gray-500">Veículo:</span>
                            <span class="font-bold text-gray-900 text-right">{{ $tripToUpdate->vehicle->model }} <br>
                                <span
                                    class="font-mono text-xs">{{ $tripToUpdate->vehicle->license_plate }}</span></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2 pt-1">
                            <span class="font-semibold text-gray-500">Condutor:</span>
                            <span class="text-right">{{ $tripToUpdate->driver->name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2 pt-1">
                            <span class="font-semibold text-gray-500">Destino:</span>
                            <span class="text-right">{{ $tripToUpdate->destination }}</span>
                        </div>
                        <div class="flex justify-between pt-1">
                            <span class="font-semibold text-gray-500">KM Saída:</span>
                            <span
                                class="font-mono font-bold text-blue-600">{{ number_format($tripToUpdate->departure_odometer, 0, ',', '.') }}
                                km</span>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="arrival_odometer" value="Quilometragem de Chegada (km)"
                            class="font-bold text-gray-700" />
                        <x-text-input type="tel" id="arrival_odometer"
                            class="mt-2 block w-full text-lg font-mono py-3"
                            x-on:input="$event.target.value = formatNumber($event.target.value)"
                            wire:model="arrival_odometer" placeholder="Ex: 45.200" autofocus />
                        <x-input-error :messages="$errors->get('arrival_odometer')" class="mt-2" />
                    </div>
                </div>

                <div
                    class="px-4 sm:px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 border-t border-gray-200">
                    <button type="button" wire:click="closeArrivalModal"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md bg-green-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all">
                        CONFIRMAR CHEGADA
                    </button>
                </div>
            </form>
        @endif
    </x-modal>
</div>
