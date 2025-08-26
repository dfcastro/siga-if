{{-- Modal Criar/Editar Veículo --}}
<x-dialog-modal wire:model="isModalOpen">
    <x-slot name="title">
        {{ $vehicle_id ? 'Editar Veículo' : 'Registrar Novo Veículo' }}
    </x-slot>

    <x-slot name="content">
        <div class="space-y-4">
            <div>
                <x-label for="license_plate" value="Placa" />
                <x-input id="license_plate" type="text" wire:model.defer="license_plate" class="mt-1 block w-full uppercase" maxlength="8" placeholder="ABC-1234" />
                @error('license_plate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label for="model" value="Modelo" />
                <x-input id="model" type="text" wire:model.defer="model" class="mt-1 block w-full" placeholder="Ex: Onix, Strada..." />
                @error('model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label for="color" value="Cor" />
                <x-input id="color" type="text" wire:model.defer="color" class="mt-1 block w-full" placeholder="Ex: Preto, Branco..." />
                @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label for="type" value="Tipo" />
                <select id="type" wire:model.defer="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Selecione</option>
                    <option value="Oficial">Oficial</option>
                    <option value="Visitante">Visitante</option>
                </select>
                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label for="driver" value="Motorista" />
                <x-input id="driver" type="text" wire:model.defer="driver_name" class="mt-1 block w-full" placeholder="Nome do motorista" />
                <small class="text-gray-500 text-xs">Se o motorista não existir, ele será cadastrado automaticamente.</small>
                @error('driver_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('isModalOpen')" class="mr-2">
            Cancelar
        </x-secondary-button>

        <x-primary-button wire:click="store">
            {{ $vehicle_id ? 'Atualizar' : 'Salvar' }}
        </x-primary-button>
    </x-slot>
</x-dialog-modal>


{{-- Modal Confirmar Exclusão --}}
<x-confirmation-modal wire:model="confirmingDelete">
    <x-slot name="title">
        Confirmar Exclusão
    </x-slot>

    <x-slot name="content">
        Tem certeza que deseja excluir este veículo?  
        Essa ação não poderá ser desfeita.
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingDelete')" class="mr-2">
            Cancelar
        </x-secondary-button>

        <x-danger-button wire:click="delete">
            Excluir
        </x-danger-button>
    </x-slot>
</x-confirmation-modal>
