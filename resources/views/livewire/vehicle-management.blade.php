<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Gerenciamento de Veículos</h1>
            <button wire:click="create" class="btn btn-primary">Cadastrar Novo Veículo</button>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Modelo</th>
                            <th>Cor</th>
                            <th>Proprietário</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $vehicle)
                            <tr>
                                <td>{{ $vehicle->license_plate }}</td>
                                <td>{{ $vehicle->model }}</td>
                                <td>{{ $vehicle->color }}</td>
                                <td>{{ $vehicle->driver->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $vehicle->type === 'Oficial' ? 'bg-info' : 'bg-light text-dark' }}">
                                        {{ $vehicle->type }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $vehicle->id }})" class="btn btn-sm btn-secondary">Editar</button>
                                    <button wire:click="confirmDelete({{ $vehicle->id }})" class="btn btn-sm btn-danger">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum veículo cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show" tabindex="-1" style="display: block;" wire:keydown.escape.window="closeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $vehicleId ? 'Editar Veículo' : 'Cadastrar Novo Veículo' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">Placa</label>
                            <input type="text" id="license_plate" class="form-control @error('license_plate') is-invalid @enderror" wire:model="license_plate"
                                x-data
                                @input="$el.value = $el.value.toUpperCase()"
                                x-mask:dynamic="$input.length > 4 && ($input.at(4) >= '0' && $input.at(4) <= '9') ? 'aaa-9999' : 'aaa9a99'">
                            @error('license_plate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Modelo</label>
                            <input type="text" id="model" class="form-control @error('model') is-invalid @enderror" wire:model="model">
                            @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Cor</label>
                            <input type="text" id="color" class="form-control @error('color') is-invalid @enderror" wire:model="color">
                            @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de Veículo</label>
                            <select id="type" class="form-select @error('type') is-invalid @enderror" wire:model.live="type">
                                <option value="Particular">Particular</option>
                                <option value="Oficial">Oficial</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($type === 'Particular')
                            <div class="mb-3">
                                <label for="driver_id" class="form-label">Proprietário (Motorista)</label>
                                <div wire:ignore>
                                    <select id="select-driver" class="form-select @error('driver_id') is-invalid @enderror">
                                        <option value="">Selecione um motorista</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('driver_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Fechar</button>
                        <button type="submit" class="btn btn-primary">{{ $vehicleId ? 'Atualizar Veículo' : 'Salvar Veículo' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    @if($isConfirmModalOpen)
    <div class="modal fade show" tabindex="-1" style="display: block;" wire:keydown.escape.window="closeConfirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    <p>Você tem certeza que deseja excluir o veículo de placa <strong>{{ $vehiclePlateToDelete ?? '' }}</strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteVehicle">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        let tomSelectInstance = null;

        const initTomSelect = (data) => {
            if (tomSelectInstance) {
                tomSelectInstance.destroy();
            }

            const el = document.getElementById('select-driver');
            if (!el) return;

            tomSelectInstance = new TomSelect(el, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results">Nenhum motorista encontrado.</div>';
                    }
                }
            });

            if (data && data.driverId) {
                tomSelectInstance.setValue(data.driverId);
            }

            tomSelectInstance.on('change', (value) => {
                @this.set('driver_id', value);
            });
        }

        Livewire.on('init-tom-select', (data) => {
            setTimeout(() => initTomSelect(data), 100);
        });
    });
</script>
@endpush