<div>
    {{-- Card do Formulário de Registro --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h4 mb-0">SIGA-IF :: Registrar Entrada de Veículo Particular</h1>
        </div>
        <div class="card-body">
            {{-- Mensagem de sucesso estilizada --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit="save">
                {{-- Linha do formulário --}}
                <div class="row">
                    {{-- Coluna para o Modelo --}}
                    <div class="col-md-6 mb-3">
                        <label for="model" class="form-label">Modelo do Veículo:</label>
                        <input type="text" id="model" class="form-control @error('vehicle_model') is-invalid @enderror" wire:model.live="vehicle_model">
                        
                        {{-- Mensagem de erro estilizada --}}
                        @error('vehicle_model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Coluna para a Placa --}}
                    <div class="col-md-6 mb-3">
                        <label for="plate" class="form-label">Placa:</label>
                        <input type="text" id="plate" class="form-control @error('license_plate') is-invalid @enderror" wire:model.live="license_plate">
                        
                        @error('license_plate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Campo de Motivo --}}
                <div class="mb-3">
                    <label for="reason" class="form-label">Motivo da Entrada:</label>
                    <textarea id="reason" class="form-control @error('entry_reason') is-invalid @enderror" wire:model.live="entry_reason"></textarea>
                    
                    @error('entry_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botão estilizado --}}
                <button type="submit" class="btn btn-primary">Registrar Entrada</button>
            </form>
        </div>
    </div>

    {{-- Adiciona um espaço entre os cards --}}
    <div class="mt-4"></div>

    {{-- Card da listagem de veículos --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="h5 mb-0">Veículos Atualmente no Campus</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Modelo</th>
                            <th>Motivo</th>
                            <th>Data de Entrada</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Usamos o @forelse para tratar o caso de a lista estar vazia --}}
                        @forelse ($currentVehicles as $entry)
                            <tr>
                                <td>{{ $entry->license_plate }}</td>
                                <td>{{ $entry->vehicle_model }}</td>
                                <td>{{ $entry->entry_reason }}</td>
                                {{-- Formatamos a data para o padrão brasileiro --}}
                                <td>{{ $entry->entry_at->format('d/m/Y H:i') }}</td>
                                <td>
                                   <button wire:click="registerExit({{ $entry->id }})" class="btn btn-sm btn-danger">Registrar Saída</button>
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