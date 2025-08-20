<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Gerenciamento de Motoristas</h1>
            <button wire:click="create" class="btn btn-primary">Cadastrar Novo Motorista</button>
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
                            <th>Nome</th>
                            <th>Documento (CPF/Matrícula)</th>
                            <th>Tipo</th>
                            <th>Autorizado Frota?</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                            <tr>
                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->document }}</td>
                                <td>{{ $driver->type }}</td>
                                <td>
                                    <span class="badge {{ $driver->is_authorized ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $driver->is_authorized ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="edit({{ $driver->id }})" class="btn btn-sm btn-secondary">Editar</button>
                                    <button wire:click="confirmDelete({{ $driver->id }})" class="btn btn-sm btn-danger">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum motorista cadastrado.</td>
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
                    <h5 class="modal-title">{{ $driverId ? 'Editar Motorista' : 'Cadastrar Novo Motorista' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit="store">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="document" class="form-label">Documento (CPF/Matrícula)</label>
                            <input type="text" id="document" class="form-control @error('document') is-invalid @enderror" wire:model="document">
                            @error('document') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo</label>
                            <select id="type" class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                <option value="">Selecione um tipo</option>
                                <option value="Servidor">Servidor</option>
                                <option value="Aluno">Aluno</option>
                                <option value="Terceirizado">Terceirizado</option>
                                <option value="Visitante">Visitante</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Checkbox de permissão visível apenas para admin/fiscal --}}
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'fiscal')
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_authorized" wire:model="is_authorized">
                                <label class="form-check-label" for="is_authorized">Autorizado a dirigir frota oficial?</label>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Fechar</button>
                        <button type="submit" class="btn btn-primary">{{ $driverId ? 'Atualizar Motorista' : 'Salvar Motorista' }}</button>
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
                    <p>Você tem certeza que deseja excluir o motorista <strong>{{ $driverNameToDelete }}</strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteDriver">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>