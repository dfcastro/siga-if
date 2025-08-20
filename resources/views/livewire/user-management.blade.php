<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-end align-items-center">
            <button wire:click="create" class="btn btn-primary">Criar Novo Usuário</button>
        </div>
        <div class="card-body">
            @if ($successMessage)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition class="alert alert-success">
                {{ $successMessage }}
            </div>
            @endif
            @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary text-capitalize">{{ $user->role }}</span></td>
                            <td>
                                <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-secondary">Editar</button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-sm btn-danger">Excluir</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhum usuário cadastrado.</td>
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
                    <h5 class="modal-title">{{ $userId ? 'Editar Usuário' : 'Criar Novo Usuário' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit="store">
                    <div class="modal-body">
                        <div class="mb-3"><label for="name" class="form-label">Nome</label><input type="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model="name">@error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" id="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">@error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="mb-3"><label for="role" class="form-label">Cargo</label><select id="role" class="form-select @error('role') is-invalid @enderror" wire:model="role">
                                <option value="">Selecione um cargo</option>
                                <option value="admin">Admin</option>
                                <option value="fiscal">Fiscal</option>
                                <option value="porteiro">Porteiro</option>
                            </select>@error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                        <div class="mb-3"><label for="password" class="form-label">Senha</label><input type="password" id="password" class="form-control @error('password') is-invalid @enderror" wire:model="password"><small class="form-text text-muted">{{ $userId ? 'Deixe em branco para não alterar a senha.' : '' }}</small>@error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Fechar</button>
                        <button type="submit" class="btn btn-primary">{{ $userId ? 'Atualizar Usuário' : 'Salvar Usuário' }}</button>
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
                    <p>Você tem certeza que deseja excluir o usuário <strong>{{ $userNameToDelete }}</strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>