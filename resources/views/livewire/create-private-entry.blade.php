<div>
    {{-- Seção de Mensagens de Alerta --}}
    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <span class="block sm:inline">{{ $successMessage }}</span>
        </div>
    @endif

    {{-- Seção de Registro de ENTRADA --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Registrar Nova Entrada de Veículo</h2>
        </div>

        <form wire:submit="save" class="p-6">
            <div class="space-y-8">
                {{-- Campo de Busca Inteligente --}}
                <div class="relative">
                    <label for="search" class="block text-sm font-medium text-gray-700">Busca Rápida (Veículo ou
                        Motorista)</label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" wire:model.live.debounce.300ms="search"
                            placeholder="Digite a placa, modelo ou nome para preencher os campos"
                            class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if (strlen($search) >= 3 && $searchResults->isNotEmpty())
                        <div
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <ul>
                                @foreach ($searchResults as $result)
                                    <li class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm"
                                        wire:click="selectVehicle({{ $result['id'] }})">
                                        {{ $result['text'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif(strlen($search) >= 3 && $searchResults->isEmpty())
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                            <p class="px-4 py-3 text-sm text-gray-500">Nenhum resultado. Preencha os campos abaixo para
                                um novo cadastro.</p>
                        </div>
                    @endif
                </div>

                {{-- Campos do Formulário --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                    {{-- Bloco da Placa --}}
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
                        <label for="license_plate" class="block text-sm font-medium text-gray-700">Placa</label>
                        <input type="text" id="license_plate" x-model="plate" placeholder="Digite a placa..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm font-mono focus:border-indigo-500 focus:ring-indigo-500 @error('license_plate') border-red-500 @enderror">
                        @error('license_plate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Modelo do
                            Veículo</label>
                        <input type="text" id="vehicle_model" wire:model="vehicle_model"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('vehicle_model') border-red-500 @enderror">
                        @error('vehicle_model')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Bloco do Motorista --}}
                <div wire:ignore>
                    <label for="select-driver-entry" class="block text-sm font-medium text-gray-700">Motorista</label>
                    <select id="select-driver-entry" class="mt-1">
                        <option value="">Selecione um motorista ou digite para cadastrar</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('selected_driver_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                @if ($isNewVisitor)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <label for="visitor_document" class="block text-sm font-medium text-blue-800">CPF do Novo
                            Visitante</label>
                        <input type="text" id="visitor_document" wire:model="visitor_document"
                            x-mask="999.999.999-99" placeholder="Digite o CPF (apenas números)"
                            class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('visitor_document') border-red-500 @enderror">
                        @error('visitor_document')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Bloco do Motivo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="entry_reason" class="block text-sm font-medium text-gray-700">Motivo da
                            Entrada</label>
                        <select id="entry_reason" wire:model.live="entry_reason"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('entry_reason') border-red-500 @enderror">
                            <option value="">Selecione um motivo</option>
                            @foreach ($predefinedReasons as $reason)
                                <option value="{{ $reason }}">{{ $reason }}</option>
                            @endforeach
                            <option value="Outro">Outro</option>
                        </select>
                        @error('entry_reason')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @if ($entry_reason === 'Outro')
                        <div>
                            <label for="other_reason" class="block text-sm font-medium text-gray-700">Especifique o
                                Motivo</label>
                            <input type="text" id="other_reason" wire:model="other_reason"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('other_reason') border-red-500 @enderror">
                            @error('other_reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-ifnmg-green px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Salvar Entrada</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Seção de Registro de SAÍDA --}}
    <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Registrar Saída (Veículos no Pátio)</h2>
        </div>
        <div class="p-6">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="exitSearch"
                    placeholder="Buscar por placa ou motorista no pátio..."
                    class="block w-full md:w-1/3 rounded-md border-gray-300 pl-10 shadow-sm mb-4 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Placa</th>
                            {{-- Coluna Adicionada --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motorista</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Entrada em</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($currentVehicles as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-gray-700">
                                    {{ $entry->license_plate }}</td>
                                {{-- Coluna Adicionada --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $entry->vehicle_model ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $entry->driver->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $entry->entry_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="registerExit({{ $entry->id }})"
                                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Registrar
                                        Saída</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Colspan Atualizado --}}
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum veículo no
                                    pátio.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            let driverSelect = new TomSelect('#select-driver-entry', {
                create: true,
                onItemAdd: function(value) {
                    @this.set('selected_driver_id', value);
                }
            });
            window.addEventListener('set-driver-select', event => {
                const driverId = event.detail[0];
                if (driverId) {
                    driverSelect.setValue(driverId);
                }
            });
            window.addEventListener('reset-form-fields', () => {
                driverSelect.clear();
            });
        });
    </script>
@endpush
