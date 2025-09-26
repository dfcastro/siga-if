<div>
    {{-- Mensagens de Alerta --}}
    @if (session()->has('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('successMessage') }}</span>
        </div>
    @endif

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
                <div class="space-y-4">
                    @forelse ($ongoingTrips as $trip)
                        {{-- RESPONSIVE: Cada linha vira um card em telas pequenas --}}
                        <div class="bg-white p-4 border border-gray-200 rounded-lg md:hidden">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-bold text-gray-800">{{ $trip->vehicle->model }}
                                    ({{ $trip->vehicle->license_plate }})
                                </div>
                                <span class="text-xs text-gray-500">Condutor: {{ $trip->driver->name }}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-2"><strong>Destino:</strong> {{ $trip->destination }}
                            </div>
                            <div class="text-sm text-gray-600 mb-4">
                                <strong>Saída:</strong> {{ $trip->departure_datetime->format('d/m/Y H:i') }}h
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
                        <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500 md:hidden">
                            Nenhuma viagem em andamento.
                        </div>
                    @endforelse
                </div>

                {{-- Tabela para Desktop --}}
                <div class="hidden md:block overflow-x-auto border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Veículo</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Condutor</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Destino</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Saída</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Porteiro</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($ongoingTrips as $trip)
                                <tr>
                                    <td class="px-6 py-4 align-middle">
                                        <div class="text-sm font-medium text-gray-900">{{ $trip->vehicle->model }}</div>
                                        <div class="text-sm text-gray-500">{{ $trip->vehicle->license_plate }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">{{ $trip->driver->name }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">{{ $trip->destination }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div>{{ $trip->departure_datetime->format('d/m/Y H:i') }}h</div>
                                        <div class="text-green-600 mt-1">
                                            {{ number_format($trip->departure_odometer, 0, ',', '.') }} km</div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">{{ $trip->guard_on_departure }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-center">
                                        <button wire:click="openArrivalModal({{ $trip->id }})"
                                            class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">Registrar
                                            Chegada</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma viagem em
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
                {{-- Layout de Cards para Mobile/Tablet --}}
                <div class="space-y-4 md:hidden">
                    @forelse ($completedTrips as $trip)
                        <div class="bg-white p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-bold text-gray-800">{{ $trip->vehicle->model }}
                                    ({{ $trip->vehicle->license_plate }})
                                </div>
                                <span
                                    class="text-sm font-medium text-gray-800">{{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                    km</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-3">Condutor: {{ $trip->driver->name }}</div>
                            <div class="text-sm text-gray-600">
                                <div><span class="font-semibold">Saída:</span>
                                    {{ $trip->departure_datetime->format('d/m/Y H:i') }}</div>
                                @if ($trip->arrival_datetime)
                                    <div><span class="font-semibold">Chegada:</span>
                                        {{ $trip->arrival_datetime->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                            Nenhuma viagem concluída encontrada.
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
                                        <div class="text-sm text-gray-500">{{ $trip->vehicle->license_plate }}</div>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">{{ $trip->driver->name }}
                                    </td>
                                    <td class="px-6 py-4 align-middle text-sm text-gray-600">
                                        <div><span class="font-semibold">S:</span>
                                            {{ $trip->departure_datetime->format('d/m/Y H:i') }}</div>
                                        @if ($trip->arrival_datetime)
                                            <div><span class="font-semibold">C:</span>
                                                {{ $trip->arrival_datetime->format('d/m/Y H:i') }}</div>
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



    {{-- Modal de Registro de Saída --}}
    @if ($isDepartureModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isDepartureModalOpen') }"
            x-show="open" @keydown.escape.window="$wire.closeDepartureModal()">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl" @click.away="$wire.closeDepartureModal()">
                @if (auth()->user()->role !== 'fiscal')
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold">Registrar Nova Saída de Veículo Oficial</h3>
                    </div>
                @endif
                <form wire:submit="storeDeparture">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Veículo --}}
                            <div>
                                <label for="select-vehicle-fleet"
                                    class="block text-sm font-medium text-gray-700">Veículo</label>
                                <div wire:ignore>
                                    <select id="select-vehicle-fleet"
                                        class="@error('vehicle_id') border-red-500 @enderror">
                                        <option value="">Selecione um veículo</option>
                                        @foreach ($officialVehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->model }}
                                                ({{ $vehicle->license_plate }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- CORREÇÃO: Mensagem de erro movida para fora do wire:ignore --}}
                                @error('vehicle_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Condutor --}}
                            <div>
                                <label for="select-driver-fleet"
                                    class="block text-sm font-medium text-gray-700">Condutor</label>
                                <div wire:ignore>
                                    <select id="select-driver-fleet"
                                        class="@error('driver_id') border-red-500 @enderror">
                                        <option value="">Selecione um condutor</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- CORREÇÃO: Mensagem de erro movida para fora do wire:ignore --}}
                                @error('driver_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Destino --}}
                            <div>
                                <label for="destination"
                                    class="block text-sm font-medium text-gray-700">Destino</label>
                                <input type="text" id="destination"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('destination') border-red-500 @enderror"
                                    wire:model="destination" maxlength="255">
                                @error('destination')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Quilometragem --}}
                            <div>
                                <label for="departure_odometer"
                                    class="block text-sm font-medium text-gray-700">Quilometragem de Saída
                                    (km)</label>
                                <input type="text" id="departure_odometer"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('departure_odometer') border-red-500 @enderror"
                                    wire:model="departure_odometer" x-mask="999999">
                                @error('departure_odometer')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Passageiros --}}
                        <div class="mt-6">
                            <label for="passengers" class="block text-sm font-medium text-gray-700">Passageiros
                                (opcional)</label>
                            <textarea id="passengers" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" wire:model="passengers"
                                rows="2" maxlength="255"></textarea>
                        </div>
                        <div class="mt-6">
                            <label for="return_observation" class="block text-sm font-medium text-gray-700">Observação
                                / Previsão de Retorno (opcional)</label>
                            <textarea id="return_observation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                wire:model="return_observation" rows="2" maxlength="255"
                                placeholder="Ex: Viagem para Salinas, retorno hoje às 18h."></textarea>
                            @error('return_observation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                        <button type="button"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                            wire:click="closeDepartureModal">Fechar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center"
                            wire:loading.attr="disabled">
                            {{-- ... svg de loading ... --}}
                            <span>Salvar Saída</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    {{-- Modal de Registro de Chegada --}}
    @if ($isArrivalModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
            x-data="{ open: @entangle('isArrivalModalOpen') }" x-show="open" @keydown.escape.window="closeArrivalModal">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="closeArrivalModal">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Registrar Chegada de Veículo</h3>
                </div>
                @if ($tripToUpdate)
                    <form wire:submit="storeArrival">
                        <div class="p-6 space-y-3">
                            <p><strong>Veículo:</strong> {{ $tripToUpdate->vehicle->model }}
                                ({{ $tripToUpdate->vehicle->license_plate }})</p>
                            <p><strong>Condutor:</strong> {{ $tripToUpdate->driver->name }}</p>
                            <p><strong>Destino:</strong> {{ $tripToUpdate->destination }}</p>
                            <p><strong>KM de Saída:</strong>
                                {{ number_format($tripToUpdate->departure_odometer, 0, ',', '.') }} km</p>
                            <hr class="border-gray-200">
                            <div>
                                <label for="arrival_odometer"
                                    class="block text-sm font-medium text-gray-700">Quilometragem de Chegada
                                    (km)</label>
                                <input type="text" {{-- Mude para "text" --}} id="arrival_odometer"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('arrival_odometer') border-red-500 @enderror"
                                    wire:model="arrival_odometer" x-mask="9999999"> {{-- PERMITE ATÉ 9.999.999 PARA A CHEGADA --}}
                                @error('arrival_odometer')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                            <button type="button"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                                wire:click="closeArrivalModal">Fechar</button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center"
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="storeArrival"
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span wire:loading.remove wire:target="storeArrival">Salvar Chegada</span>
                                <span wire:loading wire:target="storeArrival">Salvando...</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            let vehicleSelect = null;
            let driverSelect = null;

            const initTomSelects = () => {
                // Destrói instâncias antigas para evitar duplicatas
                if (vehicleSelect) vehicleSelect.destroy();
                if (driverSelect) driverSelect.destroy();

                // Inicializa o seletor de VEÍCULOS
                const vehicleEl = document.getElementById('select-vehicle-fleet');
                if (vehicleEl) {
                    vehicleSelect = new TomSelect(vehicleEl, {});
                    vehicleSelect.on('change', (value) => {
                        @this.set('vehicle_id', value,
                            false); // o 'false' evita um request desnecessário
                    });
                }

                // Inicializa o seletor de CONDUTORES
                const driverEl = document.getElementById('select-driver-fleet');
                if (driverEl) {
                    driverSelect = new TomSelect(driverEl, {});
                    driverSelect.on('change', (value) => {
                        @this.set('driver_id', value, false);
                    });
                }
            };

            // Ouve o evento do PHP para iniciar/reiniciar os seletores
            window.addEventListener('init-fleet-selectors', () => {
                setTimeout(initTomSelects, 100);
            });

            // Limpa as seleções quando o modal é fechado (opcional, mas boa prática)
            window.addEventListener('close-departure-modal', () => {
                if (vehicleSelect) vehicleSelect.clear();
                if (driverSelect) driverSelect.clear();
            });
        });
    </script>
@endpush
