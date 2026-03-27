<div x-data="{ tab: 'andamento' }">
    {{-- Mensagens de Alerta Estilizadas --}}
    @if (session()->has('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity.duration.500ms
            class="bg-green-50 border border-green-200 border-l-4 border-l-green-500 text-green-800 p-4 rounded-xl relative mb-6 shadow-sm flex items-center"
            role="alert">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm uppercase tracking-wider text-green-900">Operação Concluída</p>
                <p class="text-sm mt-0.5">{{ session('successMessage') }}</p>
            </div>
        </div>
    @endif

    @if (auth()->user()->role !== 'fiscal')

        {{-- Cabeçalho e Botão Principal --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight">Diário de Bordo Oficial</h2>
                <p class="text-sm text-gray-500 font-medium mt-1">Gerencie a saída e chegada da frota da instituição.
                </p>
            </div>
            <button wire:click="create"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 transition-all transform active:scale-95 tracking-wide">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                REGISTRAR NOVA SAÍDA
            </button>
        </div>

        {{-- NAVEGAÇÃO DAS ABAS (TABS) EM DESTAQUE --}}
        <div class="bg-white shadow-sm sm:rounded-t-xl border-b border-gray-200 mb-6">
            <nav class="flex" aria-label="Tabs">
                <button @click="tab = 'andamento'"
                    :class="tab === 'andamento' ? 'border-blue-600 text-blue-600 bg-blue-50/30' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="w-1/2 py-5 px-2 text-center border-b-4 font-bold text-sm sm:text-base transition-all duration-200 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <div class="flex items-center gap-2">
                        <span>Em Andamento</span>
                        @if (count($ongoingTrips) > 0)
                            <span
                                class="bg-red-500 text-white py-0.5 px-2.5 rounded-full text-[11px] font-black shadow-sm flex items-center justify-center">
                                {{ count($ongoingTrips) }}
                            </span>
                        @endif
                    </div>
                </button>
                <button @click="tab = 'concluidas'"
                    :class="tab === 'concluidas' ? 'border-blue-600 text-blue-600 bg-blue-50/30' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="w-1/2 py-5 px-2 text-center border-b-4 font-bold text-sm sm:text-base transition-all duration-200 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Viagens Concluídas</span>
                </button>
            </nav>
        </div>

        <div>
            {{-- ========================================================= --}}
            {{-- ABA 1: VIAGENS EM ANDAMENTO --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'andamento'" x-transition.opacity.duration.300ms
                class="bg-white overflow-hidden shadow-md sm:rounded-b-xl sm:rounded-t-none rounded-xl border border-gray-100">

                {{-- Tabela para Desktop --}}
                <div class="hidden lg:block overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Veículo</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Condutor</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Destino / Obs</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Saída / KM</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ongoingTrips as $trip)
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="bg-gray-100 border border-gray-300 rounded px-2.5 py-1 shadow-sm">
                                                <span
                                                    class="font-mono font-bold text-lg text-gray-900 tracking-wider block">{{ $trip->vehicle->license_plate }}</span>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-gray-600">{{ $trip->vehicle->model }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-700">
                                        <span
                                            class="font-bold text-gray-800">{{ $trip->driver ? $trip->driver->name : 'N/D' }}</span>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600 max-w-[250px] truncate">
                                        <span class="font-medium text-gray-800 block truncate"
                                            title="{{ $trip->destination }}">{{ $trip->destination }}</span>
                                        @if ($trip->return_observation)
                                            <div class="text-xs text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded mt-1 border border-yellow-100 inline-block truncate max-w-full"
                                                title="{{ $trip->return_observation }}">
                                                <span class="font-bold">Obs:</span> {{ $trip->return_observation }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span
                                                class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/y H:i') }}</span>
                                        </div>
                                        <div class="text-xs font-mono text-gray-500 ml-5">
                                            {{ number_format($trip->departure_odometer, 0, ',', '.') }} km
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-right">
                                        <button wire:click="openArrivalModal({{ $trip->id }})"
                                            class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-green-600 border border-green-300 shadow-sm hover:bg-green-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all opacity-90 group-hover:opacity-100 active:scale-95">
                                            Registrar Chegada
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div
                                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Nenhuma viagem em andamento</h3>
                                        <p class="mt-1 text-sm text-gray-500">Toda a frota oficial encontra-se no
                                            pátio.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards para Mobile/Tablet --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 md:hidden">
                    @forelse ($ongoingTrips as $trip)
                        <div
                            class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col justify-between relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500"></div>

                            <div class="pl-3">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="bg-gray-100 border border-gray-300 rounded px-2.5 py-1 shadow-sm">
                                        <span
                                            class="font-mono font-bold text-xl text-gray-900 tracking-wider">{{ $trip->vehicle->license_plate }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-2 py-1 rounded-md border border-blue-100">
                                            {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('H:i') }}h
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-3 mb-4">
                                    <p class="text-sm text-gray-600 font-medium flex items-center gap-2">🚗
                                        {{ $trip->vehicle->model }}</p>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-1.5">
                                        <p class="font-bold text-gray-800 text-sm">👤
                                            {{ $trip->driver ? $trip->driver->name : 'N/D' }}</p>
                                        <p class="text-xs text-gray-600">📍 {{ $trip->destination }}</p>
                                        <p class="text-xs text-gray-600">🛣️ KM Saída: <span
                                                class="font-mono font-bold">{{ number_format($trip->departure_odometer, 0, ',', '.') }}</span>
                                        </p>
                                    </div>
                                    @if ($trip->return_observation)
                                        <div
                                            class="bg-yellow-50 p-2 rounded-lg border border-yellow-100 text-xs text-yellow-800">
                                            <span class="font-bold block mb-0.5">Observação inicial:</span>
                                            {{ $trip->return_observation }}
                                        </div>
                                    @endif
                                </div>

                                <button wire:click="openArrivalModal({{ $trip->id }})"
                                    class="w-full flex justify-center items-center gap-2 rounded-xl bg-green-50 border border-green-300 px-4 py-3 text-sm font-bold text-green-700 shadow-sm hover:bg-green-600 hover:text-white transition-colors active:scale-95">
                                    Registrar Chegada
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-1 sm:col-span-2 text-center py-12 bg-gray-50 rounded-xl border-2 border-gray-200 border-dashed">
                            <p class="text-gray-500 font-medium">Nenhuma viagem em andamento.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ========================================================= --}}
            {{-- ABA 2: VIAGENS CONCLUÍDAS --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'concluidas'" style="display: none;"
                class="bg-white overflow-hidden shadow-md sm:rounded-b-xl sm:rounded-t-none rounded-xl border border-gray-100">

                <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-end">
                    <div class="relative w-full md:w-80">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar destino, veículo, motorista..."
                            class="block w-full rounded-xl border-gray-300 pl-11 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 bg-white">
                    </div>
                </div>

                {{-- Tabela para Desktop --}}
                <div class="hidden lg:block overflow-x-auto border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Veículo</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Condutor</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Destino / Período</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Distância</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($completedTrips as $trip)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-gray-100 border border-gray-300 rounded px-2 py-1">
                                                <span
                                                    class="font-mono font-bold text-sm text-gray-900 tracking-wider block">{{ $trip->vehicle->license_plate }}</span>
                                            </div>
                                            <span
                                                class="text-xs font-medium text-gray-600 truncate max-w-[120px]">{{ $trip->vehicle->model }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-800 font-medium">
                                        {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="font-bold text-gray-800 truncate max-w-[250px]"
                                                title="{{ $trip->destination }}">{{ $trip->destination }}</span>
                                            <span class="text-[11px] text-gray-500 font-medium flex gap-3">
                                                <span>🟢
                                                    {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m H:i') }}</span>
                                                <span>🔴
                                                    {{ $trip->arrival_datetime ? \Carbon\Carbon::parse($trip->arrival_datetime)->format('d/m H:i') : '-' }}</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm">
                                        <span
                                            class="bg-gray-100 text-gray-800 font-bold px-2.5 py-1 rounded-md border border-gray-200 shadow-sm">
                                            {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                            km
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Nenhuma viagem
                                        concluída encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Cards Mobile --}}
                <div class="grid grid-cols-1 gap-4 p-4 lg:hidden">
                    @forelse ($completedTrips as $trip)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gray-400"></div>
                            <div class="pl-2">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="bg-gray-100 border border-gray-300 rounded px-2 py-0.5 shadow-sm">
                                        <span
                                            class="font-mono font-bold text-base text-gray-900 tracking-wider">{{ $trip->vehicle->license_plate }}</span>
                                    </div>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded-md border border-gray-200">
                                        {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                        km
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-600 font-medium truncate">🚗
                                        {{ $trip->vehicle->model }}</p>
                                    <div
                                        class="bg-gray-50 p-2.5 rounded-lg border border-gray-100 text-xs text-gray-600 space-y-1.5">
                                        <p class="font-bold text-gray-800 text-sm">👤
                                            {{ $trip->driver ? $trip->driver->name : 'N/D' }}</p>
                                        <p class="font-semibold text-gray-800">📍 {{ $trip->destination }}</p>
                                        <div class="grid grid-cols-2 gap-2 mt-1 pt-1 border-t border-gray-200">
                                            <p>🟢 Saída: <br><span
                                                    class="font-medium">{{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/y H:i') }}</span>
                                            </p>
                                            <p>🔴 Chegada: <br><span
                                                    class="font-medium">{{ $trip->arrival_datetime ? \Carbon\Carbon::parse($trip->arrival_datetime)->format('d/m/y H:i') : '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-gray-200 border-dashed">
                            <p class="text-gray-500 font-medium">Nenhuma viagem concluída encontrada.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-6 bg-white border-t border-gray-100">
                    {{ $completedTrips->links() }}
                </div>
            </div>
        </div>
    @else
        {{-- Mensagem para o fiscal --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-xl relative shadow-sm flex items-start gap-4"
            role="alert">
            <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="block font-bold mb-1">Acesso Restrito</span>
                <span class="block text-sm sm:inline">Você tem permissão apenas para visualização. O registro de
                    entradas e saídas é restrito aos porteiros e administradores.</span>
            </div>
        </div>
    @endif


    {{-- ================================================================= --}}
    {{-- MODAL DE REGISTRO DE SAÍDA --}}
    {{-- ================================================================= --}}
    <x-modal wire:model.live="isDepartureModalOpen" maxWidth="4xl">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800">Registrar Saída da Frota Oficial</h3>
        </div>
        <form wire:submit="storeDeparture" novalidate>
            <div class="p-6 sm:p-8" x-data="{
                formatNumber(value) {
                    if (!value) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    let limited = clean.substring(0, 7);
                    return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-8">

                    {{-- Viatura Oficial (Select Direto) --}}
                    <div>
                        <x-input-label for="vehicle_id" value="Viatura Oficial"
                            class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2" />
                        <select id="vehicle_id" wire:model.live="vehicle_id"
                            class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3 bg-gray-50">
                            <option value="">Selecione o veículo...</option>
                            @foreach ($officialVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} -
                                    {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2 font-semibold" />
                    </div>

                    {{-- Condutor (Select Direto) --}}
                    <div>
                        <x-input-label for="driver_id" value="Motorista / Condutor"
                            class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2" />
                        <select id="driver_id" wire:model="driver_id"
                            class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3 bg-gray-50">
                            <option value="">Selecione o condutor...</option>
                            @foreach ($authorizedDrivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-2 font-semibold" />
                    </div>

                    {{-- Destino --}}
                    <div>
                        <x-input-label for="destination" value="Destino"
                            class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2" />
                        <x-text-input type="text" id="destination"
                            class="block w-full text-lg py-3 rounded-xl bg-gray-50" wire:model="destination"
                            maxlength="255" placeholder="Ex: Reitoria, Prefeitura, etc..." />
                        <x-input-error :messages="$errors->get('destination')" class="mt-2 font-semibold" />
                    </div>

                    {{-- Odômetro --}}
                    <div>
                        <x-input-label for="departure_odometer" value="Quilometragem de Saída (km)"
                            class="font-bold text-gray-600 uppercase tracking-wider text-xs mb-2" />
                        <x-text-input type="tel" id="departure_odometer"
                            class="block w-full font-mono text-xl py-3 rounded-xl bg-gray-50"
                            x-on:input="$event.target.value = formatNumber($event.target.value)"
                            wire:model="departure_odometer" placeholder="000.000" />
                        @if ($lastOdometer !== null)
                            <p
                                class="text-xs text-blue-600 mt-2 font-bold flex items-center bg-blue-50 p-1.5 rounded inline-block">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Última KM: {{ number_format($lastOdometer, 0, ',', '.') }}
                            </p>
                        @endif
                        <x-input-error :messages="$errors->get('departure_odometer')" class="mt-2 font-semibold" />
                    </div>
                </div>

                {{-- Campos Opcionais --}}
                <div class="mt-6 sm:mt-8 border-t border-gray-100 pt-6">
                    <x-input-label for="passengers" value="Passageiros Transportados (Opcional)"
                        class="font-bold text-gray-500 uppercase tracking-wider text-xs mb-2" />
                    <textarea id="passengers"
                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50"
                        wire:model="passengers" rows="2" maxlength="1000" placeholder="Nomes separados por vírgula..."></textarea>
                </div>
                <div class="mt-5">
                    <x-input-label for="return_observation"
                        value="Observação Inicial / Previsão de Retorno (Opcional)"
                        class="font-bold text-gray-500 uppercase tracking-wider text-xs mb-2" />
                    <textarea id="return_observation"
                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm bg-gray-50"
                        wire:model="return_observation" rows="2" maxlength="1000"
                        placeholder="Ex: Veículo saiu com pouco combustível; Retorna amanhã."></textarea>
                </div>
            </div>

            {{-- Botões do Modal --}}
            <div
                class="px-6 py-5 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse justify-start gap-3 border-t rounded-b-xl">
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto flex items-center justify-center rounded-xl bg-blue-600 px-8 py-3.5 text-sm font-black text-white shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 transition-all transform active:scale-95">
                    <svg wire:loading wire:target="storeDeparture" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    SALVAR SAÍDA
                </button>
                <button type="button" wire:click="closeDepartureModal"
                    class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ================================================================= --}}
    {{-- MODAL DE REGISTRO DE CHEGADA --}}
    {{-- ================================================================= --}}
    <x-modal wire:model.live="isArrivalModalOpen" maxWidth="md">
        @if ($tripToUpdate)
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-xl">
                <h3 class="text-xl font-black text-gray-800">Registrar Chegada</h3>
                <button wire:click="closeArrivalModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="storeArrival" x-data="{
                formatNumber(value) {
                    if (!value) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    let limited = clean.substring(0, 7);
                    return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <div class="p-6 sm:p-8">

                    {{-- Destaque Gigante da Placa para Evitar Erros --}}
                    <div
                        class="bg-gray-100 border border-gray-300 rounded-xl p-4 mb-6 flex flex-col items-center justify-center shadow-inner">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Confirmar
                            Veículo</span>
                        <span
                            class="font-mono font-black text-3xl text-gray-900 tracking-widest">{{ $tripToUpdate->vehicle->license_plate }}</span>
                        <span
                            class="text-sm font-medium text-gray-600 mt-1">{{ $tripToUpdate->vehicle->model }}</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 space-y-3 text-sm text-gray-700">
                        <div class="flex justify-between border-b border-gray-200 pb-2">
                            <span class="font-bold text-gray-500 uppercase text-xs">Condutor</span>
                            <span class="font-bold text-gray-900 text-right">{{ $tripToUpdate->driver->name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-2 pt-1">
                            <span class="font-bold text-gray-500 uppercase text-xs">Destino</span>
                            <span class="text-right font-medium">{{ $tripToUpdate->destination }}</span>
                        </div>
                        <div class="flex justify-between pt-1 items-center">
                            <span class="font-bold text-gray-500 uppercase text-xs">KM de Saída</span>
                            <span
                                class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100">{{ number_format($tripToUpdate->departure_odometer, 0, ',', '.') }}
                                km</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="arrival_odometer" value="Qual a KM Atual de Chegada?"
                            class="font-black text-gray-800 text-base mb-2" />
                        <x-text-input type="tel" id="arrival_odometer"
                            class="mt-2 block w-full text-2xl font-mono py-4 text-center rounded-xl bg-blue-50 border-blue-300 focus:ring-blue-500 focus:border-blue-500 shadow-inner"
                            x-on:input="$event.target.value = formatNumber($event.target.value)"
                            wire:model="arrival_odometer" placeholder="000.000" autofocus autocomplete="off" />
                        <x-input-error :messages="$errors->get('arrival_odometer')" class="mt-2 font-bold" />
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <x-input-label for="arrival_observation" value="Houve alguma ocorrência no retorno?"
                            class="font-bold text-gray-600 text-sm mb-2" />
                        <textarea id="arrival_observation"
                            class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 text-sm bg-gray-50"
                            wire:model="return_observation" rows="3" maxlength="1000"
                            placeholder="Opcional. Ex: O pneu furou, farol queimou, etc..."></textarea>
                        <x-input-error :messages="$errors->get('return_observation')" class="mt-2 font-bold" />
                    </div>
                </div>

                <div
                    class="px-6 py-5 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse justify-start gap-3 border-t border-gray-200 rounded-b-xl">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full sm:w-1/2 flex justify-center items-center rounded-xl bg-green-600 px-6 py-3.5 text-sm font-black text-white shadow-md hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-offset-2 transition-all transform active:scale-95 uppercase tracking-wide">
                        Confirmar Chegada
                    </button>
                    <button type="button" wire:click="closeArrivalModal"
                        class="w-full sm:w-1/2 inline-flex justify-center rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all uppercase tracking-wide">
                        Cancelar
                    </button>
                </div>
            </form>
        @endif
    </x-modal>
</div>
