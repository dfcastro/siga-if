<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Preparando Relatório para:
                <span class="font-bold text-indigo-600">{{ $selectedVehicleEntries->first()->vehicle->model }} -
                    {{ $selectedVehicleEntries->first()->vehicle->license_plate }}</span>
            </h3>
            <p class="text-sm text-gray-500">
                {{ $selectedVehicleEntries->count() }} registo(s) encontrado(s) para o período selecionado.
            </p>
        </div>
        <button wire:click="clearSelectedVehicle" class="text-sm text-gray-600 hover:text-gray-900">&larr; Voltar para a
            lista</button>
    </div>

    {{-- Tabela de Detalhes dos Registos --}}
    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Data/Hora da Saída</th>
                    <th class="px-6 py-3">Condutor</th>
                    <th class="px-6 py-3">Destino</th>
                    <th class="px-6 py-3">Data/Hora da Chegada</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($selectedVehicleEntries as $entry)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">{{ $entry->driver->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $entry->destination }}</td>
                        <td class="px-6 py-4">
                            {{ $entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum registo para este veículo
                            no período selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Seção de Submissão --}}
    <div class="mt-6 pt-6 border-t">
        <h4 class="font-semibold text-md text-gray-800">Submeter Relatório para Fiscal</h4>
        <div class="mt-4">
            <x-input-label for="observation" value="Observações (opcional)" />
            <textarea wire:model="observation" id="observation" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('observation')
                <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            {{--
                CORREÇÃO APLICADA AQUI:
                - O botão agora usa wire:click para chamar a ação 'confirmSubmission' do Livewire.
                - Ele não submete mais um formulário HTML, eliminando a causa da lentidão.
            --}}
            <x-primary-button wire:click="confirmSubmission('official')" wire:loading.attr="disabled">
                <div wire:loading wire:target="confirmSubmission('official')"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5" />
                    </svg>
                </div>
                Submeter Relatório
            </x-primary-button>
        </div>
    </div>
</div>
