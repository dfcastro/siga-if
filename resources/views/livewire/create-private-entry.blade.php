<div>
    {{-- MENSAGEM DE SUCESSO --}}
    @if ($successMessage)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="alert alert-success"
        role="alert">
        {{ $successMessage }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h4 mb-0">Registro Entrada e Saída</h1>
        </div>
        <div class="card-body">
            {{-- SEÇÃO DE BUSCA --}}
            <div class="mb-3">
                <label for="search" class="form-label">Busca Rápida (Placa, Modelo ou Motorista)</label>
                <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Digite 3 ou mais caracteres...">

                {{-- LISTA DE RESULTADOS DA BUSCA --}}
                @if(!empty($searchResults))
                <div class="list-group mt-1">
                    @foreach ($searchResults as $result)
                    <button type="button" class="list-group-item list-group-item-action" wire:click="selectVehicle({{ $result['id'] }})">
                        {{ $result['text'] }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <hr>

            {{-- FORMULÁRIO DE ENTRADA --}}
            <form wire:submit="save">
                <h2 class="h5">Dados da Entrada</h2>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="plate" class="form-label">Placa</label>
                        <input type="text" id="plate" class="form-control @error('license_plate') is-invalid @enderror" wire:model.live.debounce.300ms="license_plate"
                            x-data
                            @input="$el.value = $el.value.toUpperCase()"
                            x-mask:dynamic="$input.length > 4 && ($input.at(4) >= '0' && $input.at(4) <= '9') ? 'aaa-9999' : 'aaa9a99'">
                        @error('license_plate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="model" class="form-label">Modelo</label>
                        <input type="text" id="model" class="form-control @error('vehicle_model') is-invalid @enderror" wire:model="vehicle_model">
                        @error('vehicle_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- NOVO CAMPO DE MOTORISTA COM BUSCA E CADASTRO --}}
                    <div class="col-md-6 mb-3" wire:ignore>
                        <label for="selected_driver_id" class="form-label">Motorista</label>
                        <select id="select-driver-entry" class="form-select @error('selected_driver_id') is-invalid @enderror">
                            <option value="">Selecione ou digite para cadastrar um motorista</option>
                            @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        @error('selected_driver_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    @if($isNewVisitor)
                    <div class="row justify-content-end" x-data x-transition>
                        <div class="col-md-6 mb-3">
                            <label for="visitor_document" class="form-label">Documento (CPF) do Visitante</label>
                            <input type="text" id="visitor_document" class="form-control @error('visitor_document') is-invalid @enderror" wire:model="visitor_document"
                                x-mask="999.999.999-99" placeholder="___.___.___-__">
                            @error('visitor_document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @endif

                    {{-- Adicione este script no FINAL do arquivo --}}

                    @push('scripts')
                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            let entryTomSelect = new TomSelect('#select-driver-entry', {
                                create: true,
                                sortField: {
                                    field: "text",
                                    direction: "asc"
                                },
                                // Adicione esta opção para traduzir
                                render: {
                                    no_results: function(data, escape) {
                                        // Personalizamos a mensagem para incluir a opção de criar
                                        return '<div class="no-results">Nenhum resultado. Pressione Enter para adicionar.</div>';
                                    }
                                }
                            });

                            entryTomSelect.on('change', (value) => {
                                @this.set('selected_driver_id', value);
                            });

                            Livewire.on('reset-form-fields', () => {
                                entryTomSelect.clear();
                            });

                            // NOVO OUVINTE DE EVENTO
                            Livewire.on('set-driver-select', (driverId) => {
                                entryTomSelect.setValue(driverId);
                            });
                        });
                    </script>
                    @endpush
                </div>
                <div class="mb-3">
                    <label for="entry_reason" class="form-label">Motivo da Entrada</label>
                    <select id="entry_reason" class="form-select @error('entry_reason') is-invalid @enderror" wire:model.live="entry_reason">
                        <option value="">Selecione um motivo...</option>
                        @foreach ($predefinedReasons as $reason)
                        <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                        <option value="Outro">Outro...</option>
                    </select>
                    @error('entry_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @if ($entry_reason === 'Outro')
                <div class="mb-3" x-data x-transition>
                    <label for="other_reason" class="form-label">Por favor, especifique o motivo:</label>
                    <input type="text" id="other_reason" class="form-control @error('other_reason') is-invalid @enderror" wire:model="other_reason">
                    @error('other_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @endif
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="save">Registrando...</span>
                    <span wire:loading.remove wire:target="save">Registrar Entrada</span>
                </button>
                <button type="button" class="btn btn-secondary" wire:click="resetForm">Limpar</button>
            </form>
        </div>
    </div>

    {{-- LISTAGEM DE VEÍCULOS NO PÁTIO (sem alterações) --}}
    <div class="mt-4"></div>
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="h5 mb-0">Veículos Atualmente no Campus</h2>
            <input type="text" class="form-control" style="max-width: 250px;" placeholder="Filtrar por placa ou motorista..." wire:model.live.debounce.300ms="exitSearch">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Modelo</th>
                            <th>Motorista</th>
                            <th>Entrada</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currentVehicles as $entry)
                        <tr>
                            {{-- Se tiver um veículo vinculado, usa os dados dele. Senão, usa o texto digitado. --}}
                            <td>{{ $entry->vehicle->license_plate ?? $entry->license_plate }}</td>
                            <td>{{ $entry->vehicle->model ?? $entry->vehicle_model }}</td>
                            <td>{{ $entry->driver->name ?? 'Não identificado' }}</td>
                            <td>{{ $entry->entry_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button wire:click="registerExit({{ $entry->id }})" class="btn btn-sm btn-danger" wire:loading.attr="disabled" wire:target="registerExit({{ $entry->id }})">
                                    Registrar Saída
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum veículo no pátio no momento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>