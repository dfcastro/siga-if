<div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm animate-fade-in">
    {{-- Cabeçalho da Seção --}}
    <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Preparando Relatório para:
                @if ($selectedVehicleEntries->isNotEmpty() && $selectedVehicleEntries->first()->vehicle)
                    <span
                        class="font-bold text-ifnmg-green block sm:inline">{{ $selectedVehicleEntries->first()->vehicle->model }}
                        - {{ $selectedVehicleEntries->first()->vehicle->license_plate }}</span>
                @endif
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                {{ $selectedVehicleEntries->count() }} registo(s) encontrado(s) para o período selecionado.
            </p>
            {{-- ADICIONADO: Total de KM para visualização móvel --}}
            <p class="text-sm font-bold text-gray-700 mt-2 block lg:hidden">
                Distância Total: {{ number_format($totalDistance, 0, ',', '.') }} km
            </p>
        </div>
        <button wire:click="clearSelectedVehicle"
            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors mt-2 sm:mt-0">&larr; Voltar
            para a lista</button>
    </div>

    {{-- Tabela para Desktop (Telas Grandes) --}}
    <div class="hidden lg:block overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Saída</th>
                    <th class="px-4 py-3">Chegada</th>
                    <th class="px-4 py-3">Condutor</th>
                    <th class="px-4 py-3">Destino</th>
                    <th class="px-4 py-3 text-center">Odômetros (Saída/Chegada)</th>
                    <th class="px-4 py-3 text-center">Distância (KM)</th>
                    {{-- REMOVIDO: Coluna de Observações --}}
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($selectedVehicleEntries as $entry)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito' }}
                        </td>
                        <td class="px-4 py-3">{{ $entry->driver->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $entry->destination }}</td>
                        <td class="px-4 py-3 text-center font-mono">
                            {{ number_format($entry->departure_odometer, 0, ',', '.') }} /
                            {{ number_format($entry->arrival_odometer, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-gray-700">
                            {{ number_format($entry->distance_traveled, 0, ',', '.') }} km</td>
                        {{-- REMOVIDO: Coluna de Observações --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum registo para este veículo
                            no período selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- ADICIONADO: Rodapé com o total de KM --}}
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-800">Distância Total Percorrida no Período:
                    </td>
                    <td class="px-4 py-3 text-center font-mono text-gray-900">
                        {{ number_format($totalDistance, 0, ',', '.') }} km</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Lista de Cards para Celular (Telas Pequenas) --}}
    <div class="block lg:hidden space-y-4">
        @forelse ($selectedVehicleEntries as $entry)
            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    {{-- Condutor e Destino --}}
                    <div class="col-span-2">
                        <p class="font-semibold text-gray-500">Condutor:</p>
                        <p class="text-gray-800">{{ $entry->driver->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="font-semibold text-gray-500">Destino:</p>
                        <p class="text-gray-800">{{ $entry->destination }}</p>
                    </div>
                    {{-- Saída e Chegada --}}
                    <div>
                        <p class="font-semibold text-gray-500">Saída:</p>
                        <p class="text-gray-800">
                            {{ \Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Chegada:</p>
                        <p class="text-gray-800">
                            {{ $entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito' }}
                        </p>
                    </div>
                    {{-- Odômetros e Distância --}}
                    <div>
                        <p class="font-semibold text-gray-500">Odômetros:</p>
                        <p class="text-gray-800 font-mono">{{ number_format($entry->departure_odometer, 0, ',', '.') }}
                            / {{ number_format($entry->arrival_odometer, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Distância:</p>
                        <p class="text-gray-800 font-mono font-bold">
                            {{ number_format($entry->distance_traveled, 0, ',', '.') }} km</p>
                    </div>

                    {{-- REMOVIDO: Seção de Observações de Retorno --}}
                </div>
            </div>
        @empty
            <div class="text-center py-8 px-4 border-2 border-dashed rounded-lg">
                <p class="text-gray-500">Nenhum registo para este veículo no período selecionado.</p>
            </div>
        @endforelse
    </div>

    {{-- Seção de Submissão --}}
    <div class="mt-6 pt-6 border-t">
        <h4 class="font-semibold text-md text-gray-800">Submeter Relatório para Fiscal</h4>
        <div class="mt-4">
            <x-input-label for="observation" value="Observações Gerais do Relatório (opcional)" />
            <textarea wire:model="observation" id="observation" maxlength="100" cols="10" rows="1"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green"></textarea>
            @error('observation')
                <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
            @enderror
        </div>
        <div class="mt-6 flex justify-end">
            <x-primary-button wire:click="confirmSubmission('official')" wire:loading.attr="disabled">
                Submeter Relatório
            </x-primary-button>
        </div>
    </div>
</div>
