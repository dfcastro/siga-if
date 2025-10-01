<div>
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-lg relative"
            role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Alerta de Saídas Particulares Pendentes --}}
    @if ($pendingPrivateEntries->isNotEmpty())
        <div class="mb-8 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-lg">Alerta: Saídas de Veículos Particulares Pendentes</p>
                    <p class="text-sm">Os veículos abaixo entraram há mais de 12 horas e podem ter saído sem registo.
                    </p>
                </div>
            </div>
            <div class="mt-4">
                @foreach ($pendingPrivateEntries as $entry)
                    <div class="border-t border-red-200 py-3 px-2 flex justify-between items-center hover:bg-red-50">
                        <div>
                            <span class="font-semibold">{{ $entry->vehicle->model ?? $entry->vehicle_model }} - Placa:
                                {{ $entry->vehicle->license_plate ?? $entry->license_plate }}</span>
                            <span class="block text-xs text-gray-600">Entrou
                                {{ \Carbon\Carbon::parse($entry->entry_at)->diffForHumans() }} por
                                {{ $entry->guard_on_entry }}.</span>
                        </div>

                        {{-- BOTÃO ATUALIZADO PARA CHAMAR O MODAL --}}
                        @php
                            $vehicleIdentifier = $entry->vehicle->license_plate ?? $entry->license_plate;
                        @endphp
                        <button
                            wire:click="confirmRegistration({{ $entry->id }}, 'private', 'exit', 'Confirmar a SAÍDA do veículo {{ $vehicleIdentifier }}?')"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Registrar Saída
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Alerta de Chegadas Oficiais Pendentes --}}
    @if ($pendingOfficialTrips->isNotEmpty())
        <div class="mb-8 bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-lg">Estado da Frota: Viagens em Andamento</p>
                    <p class="text-sm">Abaixo estão todos os veículos oficiais que saíram e ainda não retornaram.</p>
                </div>
            </div>
            <div class="mt-4">
                @foreach ($pendingOfficialTrips as $trip)
                    <div class="border-t border-blue-200 py-3 px-2 flex justify-between items-center hover:bg-blue-50">
                        <div>
                            <span class="font-semibold text-gray-900">{{ $trip->vehicle?->model }}
                                ({{ $trip->vehicle?->license_plate }})
                            </span>
                            <span class="block text-xs text-gray-600">Saiu
                                {{ \Carbon\Carbon::parse($trip->departure_datetime)->diffForHumans() }}.</span>
                        </div>

                        {{-- BOTÃO ATUALIZADO PARA CHAMAR O MODAL --}}
                        @php
                            $vehicleIdentifier = $trip->vehicle?->license_plate ?? 'Veículo Removido';
                        @endphp
                        <button
                            wire:click="confirmRegistration({{ $trip->id }}, 'official', 'arrival', 'Confirmar a CHEGADA do veículo {{ $vehicleIdentifier }}?')"
                            class="ml-4 flex-shrink-0 bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Registrar Chegada
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    {{-- MODAL CENTRALIZADO EM AÇÃO --}}
    {{-- MODAL CENTRALIZADO EM AÇÃO --}}
    <x-confirmation-dialog wire:model="isConfirmModalOpen">
        <x-slot name="title">
            Confirmar Registro
        </x-slot>

        <x-slot name="content">
            @if ($itemToConfirm)
                <p class="text-sm text-gray-700">
                    Tem a certeza de que deseja registrar a
                    <strong>{{ $actionType == 'exit' ? 'SAÍDA' : 'CHEGADA' }}</strong>
                    do veículo?
                </p>
                <div class="mt-4 bg-gray-50 p-4 rounded-lg border">
                    <p><strong>Placa:</strong>
                        {{ $itemToConfirm->vehicle->license_plate ?? $itemToConfirm->license_plate }}</p>
                    <p><strong>Modelo:</strong> {{ $itemToConfirm->vehicle->model ?? $itemToConfirm->vehicle_model }}
                    </p>

                    {{-- LINHA ADICIONADA AQUI --}}
                    <p><strong>Condutor:</strong> {{ $itemToConfirm->driver->name ?? 'Não informado' }}</p>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeConfirmModal">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="executeRegistration">
                Confirmar
            </x-danger-button>
        </x-slot>
    </x-confirmation-dialog>
</div>
