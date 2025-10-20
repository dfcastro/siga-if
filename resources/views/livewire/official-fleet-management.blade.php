<div>
    {{-- Mensagens de Alerta --}}
    @if (session()->has('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('successMessage') }}</span>
        </div>
    @endif
    @if (auth()->user()->role !== 'fiscal')
        {{-- Card Principal --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div
                class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <h2 class="text-xl font-semibold text-gray-800">Diário de Bordo - Frota Oficial</h2>
                <x-primary-button wire:click="create">
                    Registrar Saída
                </x-primary-button>
            </div>

            <div class="p-6">
                {{-- Seção de Viagens em Andamento --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Viagens em Andamento</h3>
                    {{-- Layout de Cards para Mobile --}}
                    <div class="space-y-4 md:hidden">
                        @forelse ($ongoingTrips as $trip)
                            <div class="bg-white p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-gray-800">{{ $trip->vehicle->model }}
                                        ({{ $trip->vehicle->license_plate }})
                                    </div>
                                    <span class="text-xs text-gray-500">Condutor:
                                        {{ $trip->driver ? $trip->driver->name : 'N/D' }}</span>
                                </div>
                                <div class="text-sm text-gray-600 mb-2"><strong>Destino:</strong>
                                    {{ $trip->destination }}
                                </div>
                                <div class="text-sm text-gray-600 mb-4">
                                    <strong>Saída:</strong>
                                    {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/Y H:i') }}h
                                    <br>
                                    <strong>KM:</strong> {{ number_format($trip->departure_odometer, 0, ',', '.') }} km
                                </div>
                                <div class="text-sm text-gray-600 mb-2"><strong>Porteiro:</strong>
                                    {{ $trip->guard_on_departure }}
                                </div>
                                <button wire:click="openArrivalModal({{ $trip->id }})"
                                    class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                    Registrar Chegada
                                </button>
                            </div>
                        @empty
                            <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                                Nenhuma viagem em andamento.
                            </div>
                        @endforelse
                    </div>

                    {{-- Tabela para Desktop --}}
                    <div class="hidden md:block overflow-x-auto border border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Veículo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Destino</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Saída</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Porteiro</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($ongoingTrips as $trip)
                                    <tr>
                                        <td class="px-6 py-4 align-middle">
                                            <div class="text-sm font-medium text-gray-900">{{ $trip->vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $trip->vehicle->license_plate }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            {{ $trip->destination }}
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            <div>
                                                {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/Y H:i') }}h
                                            </div>
                                            <div class="text-green-600 mt-1">
                                                {{ number_format($trip->departure_odometer, 0, ',', '.') }} km</div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            {{ $trip->guard_on_departure }}</td>
                                        <td class="px-6 py-4 align-middle text-center">
                                            <button wire:click="openArrivalModal({{ $trip->id }})"
                                                class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">Registrar
                                                Chegada</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma viagem em
                                            andamento.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                {{-- Seção de Viagens Recentes --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Últimas Viagens Concluídas</h3>
                    <div class="mb-4">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por destino, veículo, placa ou motorista..."
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="hidden md:block overflow-x-auto border border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Veículo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Período da Viagem</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Distância</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($completedTrips as $trip)
                                    <tr>
                                        <td class="px-6 py-4 align-middle">
                                            <div class="text-sm font-medium text-gray-900">{{ $trip->vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $trip->vehicle->license_plate }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            {{ $trip->driver ? $trip->driver->name : 'N/D' }}
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                            <div><span class="font-semibold">S:</span>
                                                {{ \Carbon\Carbon::parse($trip->departure_datetime)->format('d/m/Y H:i') }}
                                            </div>
                                            @if ($trip->arrival_datetime)
                                                <div><span class="font-semibold">C:</span>
                                                    {{ \Carbon\Carbon::parse($trip->arrival_datetime)->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 align-middle text-sm font-medium text-gray-800">
                                            {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                            km</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhuma viagem
                                            concluída encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $completedTrips->links() }}
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
    {{-- Modal de Registro de Saída --}}
    <x-modal wire:model.live="isDepartureModalOpen" maxWidth="4xl">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Registrar Nova Saída de Veículo Oficial</h3>
        </div>
        <form wire:submit="storeDeparture" novalidate>
            <div class="p-6" x-data="{
                formatNumber(value) {
                    if (!value) return '';
                    let clean = value.toString().replace(/[^0-9]/g, '');
                    let limited = clean.substring(0, 7); // Limita a 7 dígitos
                    return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- CAMPO DE BUSCA DE VEÍCULO (igual ao de veículos privados) --}}
                    <div x-data="{ open: @entangle('show_vehicle_dropdown') }" @click.away="open = false" class="relative">
                        <x-input-label for="vehicle_search" :value="__('Veículo')" />
                        <x-text-input type="text" id="vehicle_search" class="mt-1 block w-full"
                            wire:model.live.debounce.300ms="vehicle_search" @focus="open = true"
                            placeholder="Digite a placa ou modelo" autocomplete="off" />

                        <div x-show="open" x-transition
                            class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @if (is_iterable($vehicle_results) && count($vehicle_results) > 0)
                                    @foreach ($vehicle_results as $vehicle)
                                        <li wire:click="selectVehicle({{ $vehicle->id }}, '{{ $vehicle->model }} ({{ $vehicle->license_plate }})')"
                                            class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm">
                                            {{ $vehicle->model }} ({{ $vehicle->license_plate }})
                                        </li>
                                    @endforeach
                                @elseif (strlen($vehicle_search) >= 2)
                                    <li class="px-4 py-3 text-sm text-gray-500">Nenhum veículo encontrado.</li>
                                @endif
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                    </div>

                    {{-- CAMPO DE BUSCA DE MOTORISTA (igual ao de veículos privados) --}}
                    <div x-data="{ open: @entangle('show_driver_dropdown') }" @click.away="open = false" class="relative">
                        <x-input-label for="driver_search" :value="__('Condutor')" />
                        <x-text-input type="text" id="driver_search" class="mt-1 block w-full"
                            wire:model.live.debounce.300ms="driver_search" @focus="open = true"
                            placeholder="Digite o nome do motorista" autocomplete="off" />

                        <div x-show="open" x-transition
                            class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @if (is_iterable($driver_results) && count($driver_results) > 0)
                                    @foreach ($driver_results as $driver)
                                        <li wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')"
                                            class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm">
                                            {{ $driver->name }}
                                        </li>
                                    @endforeach
                                @elseif (strlen($driver_search) >= 2)
                                    <li class="px-4 py-3 text-sm text-gray-500">Nenhum motorista encontrado.</li>
                                @endif
                            </ul>
                        </div>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-2" />
                    </div>

                    {{-- Outros Campos --}}
                    <div>
                        <x-input-label for="destination" :value="__('Destino')" />
                        <x-text-input type="text" id="destination" class="mt-1 block w-full"
                            wire:model="destination" maxlength="255" />
                        <x-input-error :messages="$errors->get('destination')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="departure_odometer" :value="__('Quilometragem de Saída (km)')" />
                        <x-text-input type="text" id="departure_odometer" class="mt-1 block w-full"
                            x-on:input="$event.target.value = formatNumber($event.target.value)"
                            wire:model="departure_odometer" />

                        {{-- >>> ADICIONE ESTE BLOCO <<< --}}
                        @if ($lastOdometer !== null)
                            <p class="text-sm text-gray-500 mt-1">
                                Último odómetro registado: <strong>{{ number_format($lastOdometer, 0, ',', '.') }}
                                    km</strong>.
                            </p>
                        @endif
                        {{-- >>> FIM DO BLOCO <<< --}}

                        <x-input-error :messages="$errors->get('departure_odometer')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-input-label for="passengers" :value="__('Passageiros (opcional)')" />
                <textarea id="passengers" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" wire:model="passengers"
                    rows="2" maxlength="1000"></textarea>
            </div>
            <div class="mt-6">
                <x-input-label for="return_observation" :value="__('Observação / Previsão de Retorno (opcional)')" />
                <textarea id="return_observation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    wire:model="return_observation" rows="2" maxlength="1000"
                    placeholder="Ex: Viagem para Salinas, retorno hoje às 18h."></textarea>
            </div>
</div>

<div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
    <x-secondary-button type="button" wire:click="closeDepartureModal">Fechar</x-secondary-button>
    <x-primary-button type="submit" wire:loading.attr="disabled">Salvar Saída</x-primary-button>
</div>
</form>
</x-modal>

{{-- Modal de Registro de Chegada --}}
<x-modal wire:model.live="isArrivalModalOpen" maxWidth="lg">
    @if ($tripToUpdate)
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Registrar Chegada de Veículo</h3>
        </div>
        <form wire:submit="storeArrival" x-data="{
            formatNumber(value) {
                if (!value) return '';
                let clean = value.toString().replace(/[^0-9]/g, '');
                let limited = clean.substring(0, 7); // Limita a 7 dígitos
                return limited.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }">
            <div class="p-6 space-y-3">
                <p><strong>Veículo:</strong> {{ $tripToUpdate->vehicle->model }}
                    ({{ $tripToUpdate->vehicle->license_plate }})</p>
                <p><strong>Condutor:</strong> {{ $tripToUpdate->driver->name }}</p>
                <p><strong>Destino:</strong> {{ $tripToUpdate->destination }}</p>
                <p><strong>KM de Saída:</strong>
                    {{ number_format($tripToUpdate->departure_odometer, 0, ',', '.') }} km</p>
                <hr class="border-gray-200">
                <div>
                    <x-input-label for="arrival_odometer" :value="__('Quilometragem de Chegada (km)')" />
                    <x-text-input type="text" id="arrival_odometer" class="mt-1 block w-full"
                        x-on:input="$event.target.value = formatNumber($event.target.value)"
                        wire:model="arrival_odometer" />
                    <x-input-error :messages="$errors->get('arrival_odometer')" class="mt-2" />
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                <x-secondary-button type="button" wire:click="closeArrivalModal">Fechar</x-secondary-button>
                <x-primary-button type="submit" class="bg-green-600 hover:bg-green-700"
                    wire:loading.attr="disabled">Salvar Chegada</x-primary-button>
            </div>
        </form>
    @endif
</x-modal>
</div>
