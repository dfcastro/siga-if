<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Diário de Bordo - Frota Oficial</h1>
            <button wire:click="create" class="btn btn-primary">Registrar Nova Saída</button>
        </div>
        <div class="card-body">
            @if ($successMessage)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition class="alert alert-success">
                {{ $successMessage }}
            </div>
            @endif

            {{-- Seção de Viagens em Andamento --}}
            <h2 class="h5">Viagens em Andamento</h2>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Veículo (Placa)</th>
                            <th>Condutor</th>
                            <th>Destino</th>
                            <th>Data de Saída</th>
                            <th>KM de Saída</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ongoingTrips as $trip)
                        <tr>
                            <td>{{ $trip->vehicle->model }} ({{ $trip->vehicle->license_plate }})</td>
                            <td>{{ $trip->driver->name }}</td>
                            <td>{{ $trip->destination }}</td>
                            <td>{{ $trip->departure_datetime->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($trip->departure_odometer, 0, ',', '.') }} km</td>
                            <td>
                                <button wire:click="openArrivalModal({{ $trip->id }})" class="btn btn-sm btn-success">Registrar Chegada</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma viagem em andamento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            {{-- Seção de Viagens Recentes --}}
            <h2 class="h5">Últimas Viagens Concluídas</h2>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Veículo (Placa)</th>
                            <th>Condutor</th>
                            <th>Destino</th>
                            <th>Saída</th>
                            <th>Chegada</th>
                            <th>Distância</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($completedTrips as $trip)
                        <tr>
                            <td>{{ $trip->vehicle->license_plate }}</td>
                            <td>{{ $trip->driver->name }}</td>
                            <td>{{ $trip->destination }}</td>
                            <td>{{ $trip->departure_datetime->format('d/m H:i') }}</td>
                            <td>{{ $trip->arrival_datetime->format('d/m H:i') }}</td>
                            <td>{{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }} km</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma viagem concluída recentemente.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($isDepartureModalOpen)
    <div class="modal fade show" tabindex="-1" style="display: block;" wire:keydown.escape.window="closeDepartureModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nova Saída de Veículo Oficial</h5>
                    <button type="button" class="btn-close" wire:click="closeDepartureModal"></button>
                </div>
                <form wire:submit="storeDeparture">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Veículo</label>
                                <div wire:ignore>
                                    <select id="select-vehicle-fleet" class="form-select @error('vehicle_id') is-invalid @enderror">
                                        <option value="">Selecione um veículo</option>
                                        @foreach ($officialVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->license_plate }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('vehicle_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="driver_id" class="form-label">Condutor</label>
                                <div wire:ignore>
                                    <select id="select-driver-fleet" class="form-select @error('driver_id') is-invalid @enderror">
                                        <option value="">Selecione um condutor</option>
                                        @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('driver_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="destination" class="form-label">Destino</label>
                                <input type="text" id="destination" class="form-control @error('destination') is-invalid @enderror" wire:model="destination">
                                @error('destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="departure_odometer" class="form-label">Quilometragem de Saída (km)</label>
                                <input type="number" id="departure_odometer" class="form-control @error('departure_odometer') is-invalid @enderror" wire:model="departure_odometer">
                                @error('departure_odometer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="passengers" class="form-label">Passageiros (opcional)</label>
                            <textarea id="passengers" class="form-control" wire:model="passengers" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDepartureModal">Fechar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="storeDeparture">Salvando...</span>
                            <span wire:loading.remove wire:target="storeDeparture">Salvar Saída</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    @if($isArrivalModalOpen)
    <div class="modal fade show" tabindex="-1" style="display: block;" wire:keydown.escape.window="closeArrivalModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Chegada de Veículo</h5>
                    <button type="button" class="btn-close" wire:click="closeArrivalModal"></button>
                </div>
                <form wire:submit="storeArrival">
                    <div class="modal-body">
                        <p><strong>Veículo:</strong> {{ $tripToUpdate->vehicle->model }} ({{ $tripToUpdate->vehicle->license_plate }})</p>
                        <p><strong>Condutor:</strong> {{ $tripToUpdate->driver->name }}</p>
                        <p><strong>Destino:</strong> {{ $tripToUpdate->destination }}</p>
                        <p><strong>KM de Saída:</strong> {{ number_format($tripToUpdate->departure_odometer, 0, ',', '.') }} km</p>
                        <hr>
                        <div class="mb-3">
                            <label for="arrival_odometer" class="form-label">Quilometragem de Chegada (km)</label>
                            <input type="number" id="arrival_odometer" class="form-control @error('arrival_odometer') is-invalid @enderror" wire:model="arrival_odometer">
                            @error('arrival_odometer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeArrivalModal">Fechar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="storeArrival">Salvando...</span>
                            <span wire:loading.remove wire:target="storeArrival">Salvar Chegada</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif


</div>
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        let vehicleSelect = null;
        let driverSelect = null;

        // Ouve o evento do PHP para iniciar/reiniciar os seletores
        Livewire.on('init-fleet-selectors', () => {

            // Adicionamos um pequeno timeout para garantir que o DOM foi atualizado
            setTimeout(() => {
                // Destrói instâncias antigas se existirem
                if (vehicleSelect) vehicleSelect.destroy();
                if (driverSelect) driverSelect.destroy();

                // Inicializa o seletor de VEÍCULOS
                vehicleSelect = new TomSelect('#select-vehicle-fleet', {
                    render: {
                        no_results: function(d, e) {
                            return '<div class="no-results">Nenhum veículo encontrado.</div>';
                        }
                    }
                });
                vehicleSelect.on('change', (value) => {
                    @this.set('vehicle_id', value);
                });

                // Inicializa o seletor de CONDUTORES
                driverSelect = new TomSelect('#select-driver-fleet', {
                    render: {
                        no_results: function(d, e) {
                            return '<div class="no-results">Nenhum condutor encontrado.</div>';
                        }
                    }
                });
                driverSelect.on('change', (value) => {
                    @this.set('driver_id', value);
                });
            }, 100); // 100 milissegundos é um tempo seguro
        });
    });
</script>
@endpush