<div>
    {{-- Alerts --}}
    @if (session('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="flex items-center bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4"
            role="alert">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('successMessage') }}</span>
        </div>
    @endif

    @if (session('errorMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="flex items-center bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4"
            role="alert">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('errorMessage') }}</span>
        </div>
    @endif

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Gerenciamento de Veículos</h2>
            <x-primary-button wire:click="create">
                Criar Novo Veículo
            </x-primary-button>
        </div>

        <div class="p-6">
            {{-- CAMPO DE BUSCA --}}
            <div class="mb-4">
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar por placa, modelo, cor ou motorista..."
                    class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            {{-- Desktop: Tabela --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motorista</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($vehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-4">{{ $vehicle->license_plate }}</td>
                                <td class="px-6 py-4">{{ $vehicle->model }}</td>
                                <td class="px-6 py-4">{{ $vehicle->color }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs font-semibold rounded-full {{ $vehicle->type === 'Oficial' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $vehicle->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">{{ $vehicle->driver->name ?? 'Sem motorista' }}</td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <x-secondary-button
                                        wire:click="edit({{ $vehicle->id }})">Editar</x-secondary-button>
                                    <x-danger-button
                                        wire:click="confirmDelete({{ $vehicle->id }})">Excluir</x-danger-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum veículo
                                    cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile/Tablet: Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($vehicles as $vehicle)
                    <div class="bg-gray-50 border rounded-lg p-4 shadow-sm">
                        <h3 class="font-semibold text-lg">{{ $vehicle->license_plate }} - {{ $vehicle->model }}</h3>
                        <p class="text-sm text-gray-600">Cor: {{ $vehicle->color }}</p>
                        <p class="text-sm text-gray-600">Tipo:
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full {{ $vehicle->type === 'Oficial' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' }}">
                                {{ $vehicle->type }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-600">Motorista: {{ $vehicle->driver->name ?? 'Sem motorista' }}</p>

                        <div class="mt-4 flex space-x-2">
                            <x-secondary-button class="flex-1"
                                wire:click="edit({{ $vehicle->id }})">Editar</x-secondary-button>
                            <x-danger-button class="flex-1"
                                wire:click="confirmDelete({{ $vehicle->id }})">Excluir</x-danger-button>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-2">Nenhum veículo cadastrado.</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

    {{-- Modal de Edição/Criação --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-data="{ open: @entangle('isModalOpen') }"
            x-show="open" @keydown.escape.window="open = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="open = false">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">{{ $vehicleId ? 'Editar Veículo' : 'Criar Novo Veículo' }}</h3>
                </div>
                <form wire:submit="store">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Coluna 1 --}}
                        <div class="space-y-4">
                            {{-- Bloco da Placa COM A SUA LÓGICA FUNCIONAL --}}
                            <div x-data="{
                                plate: @entangle('license_plate'),
                                formatPlate() {
                                    let clean = String(this.plate || '').toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 7);
                                    let formatted = '';
                                    for (let i = 0; i < clean.length; i++) {
                                        const char = clean[i];
                                        if (i <= 2 && /[A-Z]/.test(char)) formatted += char;
                                        else if (i === 3 && /[0-9]/.test(char)) formatted += char;
                                        else if (i === 4 && /[A-Z0-9]/.test(char)) formatted += char;
                                        else if (i >= 5 && /[0-9]/.test(char)) formatted += char;
                                    }
                                    if (/^[A-Z]{3}[0-9]{4}$/.test(formatted)) {
                                        formatted = formatted.substring(0, 3) + '-' + formatted.substring(3);
                                    }
                                    this.plate = formatted;
                                }
                            }" x-init="$watch('plate', () => formatPlate())">
                                <label for="license_plate_modal"
                                    class="block text-sm font-medium text-gray-700">Placa</label>
                                <input type="text" id="license_plate_modal" x-model="plate"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm uppercase font-mono @error('license_plate') border-red-500 @enderror">
                                @error('license_plate')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">Modelo</label>
                                <input type="text" id="model"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('model') border-red-500 @enderror"
                                    wire:model="model" maxlength="20" oninput="this.value = this.value.toUpperCase()">
                                {{-- ADICIONE ESTA LINHA --}}
                                @error('model')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="color_selector" class="block text-sm font-medium text-gray-700">Cor</label>
                                <div wire:ignore>
                                    <select id="color_selector" wire:model="color"
                                        placeholder="Selecione uma cor...">
                                        <option value="">Selecione uma cor...</option>
                                        @foreach ($commonColors as $colorOption)
                                            <option value="{{ $colorOption }}">{{ $colorOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('color')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        {{-- Coluna 2 --}}
                        <div class="space-y-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                                <select id="type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('type') border-red-500 @enderror"
                                    wire:model="type">
                                    <option value="">Selecione o tipo</option>
                                    @if (in_array(auth()->user()->role, ['admin', 'fiscal']))
                                        <option value="Oficial">Oficial</option>
                                    @endif
                                    <option value="Particular">Particular</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="driver_id"
                                    class="block text-sm font-medium text-gray-700">Motorista</label>
                                <div wire:ignore>
                                    <select id="driver_id" wire:model="driver_id"
                                        placeholder="Selecione um motorista...">
                                        <option value="">Selecione um motorista...</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('driver_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                        <x-secondary-button type="button" @click="open = false">Fechar</x-secondary-button>
                        <x-primary-button type="submit">{{ $vehicleId ? 'Atualizar' : 'Salvar' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal de Confirmação de Exclusão --}}
    @if ($isConfirmModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
            x-data="{ open: @entangle('isConfirmModalOpen') }" x-show="open" @keydown.escape.window="open = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md" @click.away="open = false">
                <div class="p-6">
                    <h3 class="text-lg font-bold">Confirmar Exclusão</h3>
                    <p class="mt-2 text-sm text-gray-600">Você tem certeza que deseja excluir o veículo de placa
                        <strong>{{ $vehiclePlateToDelete }}</strong>?
                    </p>
                    <p class="mt-1 text-sm text-red-600">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                    <x-secondary-button @click="open = false">Cancelar</x-secondary-button>
                    <x-danger-button wire:click="deleteVehicle">Confirmar Exclusão</x-danger-button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    {{-- SEU SCRIPT ORIGINAL E FUNCIONAL DO TOMSELECT --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            let tomSelectInstance = null;
            window.addEventListener('init-tom-select', event => {
                setTimeout(() => {
                    const el = document.getElementById('driver_id');
                    if (el && !el.tomselect) {
                        tomSelectInstance = new TomSelect(el, {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            }
                        });
                    }
                }, 100);
            });
            window.addEventListener('destroy-tom-select', event => {
                const el = document.getElementById('driver_id');
                if (el && el.tomselect) {
                    el.tomselect.destroy();
                }
            });
        });
    </script>
@endpush
