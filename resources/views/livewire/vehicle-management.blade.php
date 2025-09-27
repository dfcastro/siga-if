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

    {{-- Card Principal --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Gerenciamento de Veículos</h2>
            <x-primary-button wire:click="create">Criar Novo Veículo</x-primary-button>
        </div>

        <div class="p-6">
            <div class="md:flex justify-between items-center mb-4">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar..."
                    class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm mb-4 md:mb-0">
                <div class="flex space-x-2">
                    <button wire:click="$set('filter', 'active')"
                        class="px-4 py-2 text-sm rounded-md shadow-sm border {{ $filter === 'active' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">Ativos</button>
                    <button wire:click="$set('filter', 'trashed')"
                        class="px-4 py-2 text-sm rounded-md shadow-sm border {{ $filter === 'trashed' ? 'bg-ifnmg-green text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">Lixeira</button>
                </div>
            </div>

            {{-- Desktop: Tabela --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full bg-white border rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proprietário
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($vehicles as $vehicle)
                            <tr class="{{ $vehicle->trashed() ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                                <td class="px-6 py-4 font-mono">{{ $vehicle->license_plate }}</td>
                                <td class="px-6 py-4">{{ $vehicle->model }}</td>
                                <td class="px-6 py-4">{{ $vehicle->type }}</td>
                                <td class="px-6 py-4">{{ $vehicle->driver->name ?? 'Sem proprietário' }}</td>
                                <td class="px-6 py-4 space-x-2 text-center">
                                    @if ($vehicle->trashed())
                                        <button wire:click="restore({{ $vehicle->id }})"
                                            class="font-medium text-green-600 hover:text-green-800">Restaurar</button>
                                        <button wire:click="confirmForceDelete({{ $vehicle->id }})"
                                            class="font-medium text-red-600 hover:text-red-800 ml-2">Excluir
                                            Perm.</button>
                                    @else
                                        <button wire:click="showHistory({{ $vehicle->id }})"
                                            class="font-medium text-blue-600 hover:text-blue-800">Histórico</button>
                                        <x-secondary-button wire:click="edit({{ $vehicle->id }})"
                                            class="ml-2">Editar</x-secondary-button>
                                        <x-danger-button
                                            wire:click="confirmDelete({{ $vehicle->id }})">Excluir</x-danger-button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum veículo
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile/Tablet: Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($vehicles as $vehicle)
                    <div
                        class="bg-gray-50 border rounded-lg p-4 shadow-sm flex flex-col justify-between {{ $vehicle->trashed() ? 'bg-red-50 opacity-70' : '' }}">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $vehicle->license_plate }} - {{ $vehicle->model }}
                            </h3>
                            {{-- ... (outros detalhes do card) ... --}}
                        </div>
                        <div class="mt-4 flex space-x-2">
                            @if ($vehicle->trashed())
                                <x-secondary-button class="flex-1"
                                    wire:click="restore({{ $vehicle->id }})">Restaurar</x-secondary-button>
                                <x-danger-button class="flex-1"
                                    wire:click="confirmForceDelete({{ $vehicle->id }})">Excluir
                                    Perm.</x-danger-button>
                            @else
                                <x-primary-button class="flex-1"
                                    wire:click="showHistory({{ $vehicle->id }})">Histórico</x-primary-button>
                                <x-secondary-button class="flex-1"
                                    wire:click="edit({{ $vehicle->id }})">Editar</x-secondary-button>
                                <x-danger-button class="flex-1"
                                    wire:click="confirmDelete({{ $vehicle->id }})">Excluir</x-danger-button>
                            @endif
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold">
                        {{ $filter === 'active' ? 'Mover para a Lixeira' : 'Confirmar Exclusão Permanente' }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Você tem certeza que deseja
                        <strong>{{ $filter === 'active' ? 'mover o veículo' : 'excluir PERMANENTEMENTE o veículo' }}</strong>
                        <strong>"{{ $vehiclePlateToDelete }}"</strong>?
                    </p>
                    @if ($filter === 'trashed')
                        <p class="mt-1 text-sm text-red-600 font-bold">Esta ação não pode ser desfeita.</p>
                    @else
                        <p class="mt-1 text-sm text-gray-500">Você poderá restaurar este item da lixeira.</p>
                    @endif
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
                    <x-secondary-button wire:click="closeConfirmModal">Cancelar</x-secondary-button>
                    <x-danger-button wire:click="{{ $filter === 'active' ? 'deleteVehicle' : 'forceDeleteVehicle' }}">
                        Confirmar
                    </x-danger-button>
                </div>
            </div>
        </div>
    @endif


    @if ($isHistoryModalOpen && $vehicleForHistory)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Histórico de Movimentação</h3>
                    <p class="text-sm text-gray-600">{{ $vehicleForHistory->model }} -
                        {{ $vehicleForHistory->license_plate }}</p>
                </div>

                <div class="p-6 overflow-y-auto">
                    @forelse ($vehicleHistory as $entry)
                        <div
                            class="border-l-4 {{ $entry['type'] === 'Oficial' ? 'border-blue-500' : 'border-green-500' }} pl-4 mb-4 pb-4 last:mb-0 last:pb-0">
                            <p class="font-semibold">Viagem {{ $entry['type'] }}</p>
                            <div class="text-sm text-gray-700 space-y-1 mt-1">
                                <p><strong>Data de Saída/Entrada:</strong>
                                    {{ \Carbon\Carbon::parse($entry['start_time'])->format('d/m/Y H:i') }}</p>
                                <p><strong>Data de Chegada/Saída:</strong>
                                    {{ $entry['end_time'] ? \Carbon\Carbon::parse($entry['end_time'])->format('d/m/Y H:i') : 'Em trânsito' }}
                                </p>
                                <p><strong>Condutor:</strong> {{ $entry['driver_name'] }}</p>
                                <p><strong>{{ $entry['type'] === 'Oficial' ? 'Destino:' : 'Motivo:' }}</strong>
                                    {{ $entry['detail'] }}</p>
                                <p><strong>Porteiro (Entrada):</strong> {{ $entry['guard_entry'] ?? 'N/A' }}</p>
                                <p><strong>Porteiro (Saída):</strong> {{ $entry['guard_exit'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500">Nenhum histórico de movimentação para este veículo.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $vehicleHistory->links() }}
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 text-right mt-auto border-t">
                    <x-secondary-button wire:click="closeHistoryModal">Fechar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif

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
