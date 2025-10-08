<div>
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Meu Relatório</h2>
            <p class="text-sm text-gray-500 mt-1">Selecione o tipo de relatório e o período. Apenas os registos efetuados
                por si serão exibidos.</p>
        </div>

        <div class="p-6">
            {{-- Filtros --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 items-end">
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                    <select wire:model.live="reportType" id="report_type"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                        <option value="particular">Minhas Entradas Particulares</option>
                        <option value="oficial">Minhas Saídas Oficiais</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
                    <input type="date" wire:model.live="startDate" id="start_date"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                    <input type="date" wire:model.live="endDate" id="end_date"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                </div>
            </div>

            {{-- Botão de Exportar --}}
            <div>
                @if ($reportType === 'particular')
                    <a href="{{ route('reports.personal.pdf', ['type' => 'private', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Exportar PDF (Particular)
                    </a>
                @else
                    <a href="{{ route('reports.personal.pdf', ['type' => 'official', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Exportar PDF (Oficial)
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabela de Resultados --}}
    @if ($results)
        <div class="mt-8 bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6">
                <div class="min-w-full overflow-hidden overflow-x-auto align-middle sm:rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            @if ($reportType === 'particular')
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veículo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condutor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Entrada/Saída</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo
                                    </th>
                                </tr>
                            @else
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veículo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condutor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Saída/Chegada</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destino
                                    </th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($results as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $result->vehicle?->model ?? $result->vehicle_model }}</div>
                                        <div class="text-sm text-gray-500 font-mono">
                                            {{ $result->vehicle?->license_plate ?? $result->license_plate }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $result->driver?->name ?? 'N/A' }}</td>

                                    {{-- CÉLULAS DE DATA CORRIGIDAS --}}
                                    @if ($reportType === 'particular')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if ($result->entry_at)
                                                Entrada: {{ $result->entry_at->format('d/m/Y H:i') }}<br>
                                            @endif
                                            @if ($result->exit_at)
                                                Saída: {{ $result->exit_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $result->entry_reason }}</td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if ($result->departure_datetime)
                                                Saída: {{ $result->departure_datetime->format('d/m/Y H:i') }}<br>
                                            @endif
                                            @if ($result->arrival_datetime)
                                                Chegada: {{ $result->arrival_datetime->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $result->destination }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum registo encontrado para os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($results->hasPages())
                    <div class="mt-4">{{ $results->links() }}</div>
                @endif
            </div>
        </div>
    @endif
</div>
