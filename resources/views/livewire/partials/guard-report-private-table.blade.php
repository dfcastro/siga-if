<div class="overflow-x-auto border rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        {{-- CABEÇALHO PARA DESKTOP --}}
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
            <tr>
                <th class="px-6 py-3">Veículo (Modelo/Placa)</th>
                <th class="px-6 py-3">Condutor</th>
                <th class="px-6 py-3">Data/Hora Entrada</th>
                {{-- NOVAS COLUNAS --}}
                <th class="px-6 py-3">Data/Hora Saída</th>
                <th class="px-6 py-3">Motivo da Entrada</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 sm:divide-y-0">
            @forelse ($privateEntries as $entry)
                {{-- Layout de Card para Celular (dentro do tbody) --}}
                <tr class="block sm:hidden border-b p-4">
                    <td class="block" colspan="5">
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Veículo:</p>
                                <p class="font-medium text-gray-800">{{ $entry->vehicle_model ?? 'N/A' }}
                                    ({{ $entry->license_plate ?? 'N/A' }})</p>
                            </div>
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Condutor:</p>
                                <p class="text-gray-800">{{ $entry->driver->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Entrada:</p>
                                <p class="text-gray-800">{{ $entry->entry_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Saída:</p>
                                <p class="text-gray-800">
                                    {{ $entry->exit_at ? $entry->exit_at->format('d/m/Y H:i') : 'No pátio' }}</p>
                            </div>
                            <div class="col-span-2 mt-2 border-t pt-2">
                                <p class="font-semibold text-gray-500">Motivo da Entrada:</p>
                                <p class="text-gray-700">{{ $entry->entry_reason ?? 'Não especificado' }}</p>
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- Layout de Tabela para Desktop --}}
                <tr class="hidden sm:table-row bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $entry->vehicle_model ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $entry->license_plate ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $entry->driver->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $entry->entry_at->format('d/m/Y H:i') }}</td>
                    {{-- NOVOS DADOS NA TABELA --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $entry->exit_at ? $entry->exit_at->format('d/m/Y H:i') : 'No pátio' }}</td>
                    <td class="px-6 py-4">{{ $entry->entry_reason ?? 'Não especificado' }}</td>
                </tr>
            @empty
                <tr class="bg-white">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum registo pendente.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
