<div>
    {{-- Cabeçalho da Página --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $header ?? 'Relatórios Gerenciais' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SEÇÃO REMOVIDA: DASHBOARD DE ANALYTICS VISUAL --}}

            {{-- SEÇÃO 2: RELATÓRIOS DETALHADOS --}}
            <div>
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                    <div class="p-6">
                        {{-- Título da Seção --}}
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-4">Gerar Relatórios e Filtrar
                            Dados da Tabela</h2>

                        {{-- Mensagem de Erro --}}
                        @if (session()->has('error'))
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded"
                                role="alert">
                                <p>{{ session('error') }}</p>
                            </div>
                        @endif

                        {{-- Formulário de Filtros (removido <form> e wire:submit) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 items-end">
                            {{-- Ajustado grid --}}
                            {{-- Tipo --}}
                            <div>
                                <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo
                                    Relatório/Tabela</label>
                                <select wire:model.live="reportType" id="report_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="official">Viagens Oficiais</option>
                                    <option value="private">Entradas Particulares</option> {{-- Ajustado value --}}
                                </select>
                            </div>

                            {{-- Mês --}}
                            <div>
                                <label for="report_month" class="block text-sm font-medium text-gray-700">Mês</label>
                                <input type="month" wire:model.live="selectedMonth" id="report_month"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm @error('selectedMonth') border-red-500 @enderror"
                                    max="{{ Carbon\Carbon::now()->subMonthNoOverflow()->format('Y-m') }}">
                                @error('selectedMonth')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Veículo --}}
                            <div>
                                <label for="vehicle_report" class="block text-sm font-medium text-gray-700">Veículo
                                    (Opcional)</label>
                                <select wire:model.live="selectedVehicle" id="vehicle_report"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="">-- Todos --</option>
                                    {{-- Usando mapWithKeys como no original --}}
                                    @foreach ($this->vehicles as $vehicleId => $vehicleDescription)
                                        <option value="{{ $vehicleId }}">{{ $vehicleDescription }}</option>
                                    @endforeach
                                </select>
                                @error('selectedVehicle')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Motorista --}}
                            <div>
                                <label for="driver_report" class="block text-sm font-medium text-gray-700">Motorista
                                    (Opcional)</label>
                                <select wire:model.live="selectedDriver" id="driver_report"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="">-- Todos --</option>
                                    {{-- Usando pluck como no original --}}
                                    @foreach ($this->drivers as $driverId => $driverName)
                                        <option value="{{ $driverId }}">{{ $driverName }}</option>
                                    @endforeach
                                </select>
                                @error('selectedDriver')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Botões de Ação PDF (Links <a> como no original) --}}
                        <div class="mt-6 flex items-center justify-end space-x-4 border-t pt-4"> {{-- Movido para o final e adicionado separador --}}
                            {{-- Botão Filtrar Tabela REMOVIDO --}}

                            {{-- Link PDF Oficial (com verificação de veículo selecionado) --}}
                            {{-- Usa $pdfStartDate e $pdfEndDate calculadas no componente --}}
                            <a href="{{ route('reports.official.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium {{ $reportType !== 'official' ? 'hidden' : '' }} @if ($reportType === 'official' && empty($selectedVehicle)) opacity-50 cursor-not-allowed @endif"
                                @if ($reportType === 'official' && empty($selectedVehicle)) onclick="event.preventDefault(); alert('Por favor, selecione um VEÍCULO OFICIAL específico para gerar este PDF.');"
                                   title="Selecione um veículo oficial para habilitar" @endif
                                wire:loading.class="opacity-50 cursor-wait" wire:target="render">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Exportar PDF Oficial (por Veículo)
                            </a>

                            {{-- Link PDF Particular --}}
                            {{-- Usa $pdfStartDate e $pdfEndDate calculadas no componente --}}
                            <a href="{{ route('reports.private.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium {{ $reportType !== 'private' ? 'hidden' : '' }}"
                                {{-- Ajustado valor --}} wire:loading.class="opacity-50 cursor-wait" wire:target="render">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Exportar PDF Particular
                            </a>
                        </div>
                        {{-- form removido --}}
                    </div> {{-- Fim p-6 filtros --}}
                </div> {{-- Fim card filtros --}}

                {{-- Tabela de Resultados Detalhados (Mantida como no seu original) --}}
                @if ($results)
                    <div class="mt-8 bg-white overflow-hidden shadow-md sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Registos Concluídos para: <span
                                    class="font-bold text-ifnmg-green">{{ $reportType === 'official' ? 'Viagens Oficiais' : 'Entradas Particulares' }}</span>
                                {{-- Exibe o mês selecionado --}}
                                <span class="text-sm font-normal text-gray-600">(Mês:
                                    {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F/Y') }})</span>
                            </h3>
                            <div class="overflow-x-auto border border-gray-200 sm:rounded-lg shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        @if ($reportType === 'official')
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Veículo</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Motorista</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Partida</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Chegada</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Destino</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">
                                                    KM Rodado</th>
                                            </tr>
                                        @else
                                            {{-- private --}}
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Veículo</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Motorista</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Entrada</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Saída</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                                    Motivo</th>
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($results as $result)
                                            <tr class="hover:bg-gray-50">
                                                {{-- Célula Veículo --}}
                                                <td class="px-4 py-4 align-top">
                                                    <div class="text-sm font-semibold">
                                                        {{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/A') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 font-mono">
                                                        {{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/A') }}
                                                    </div>
                                                </td>
                                                {{-- Célula Motorista --}}
                                                <td class="px-4 py-4 align-top text-sm">
                                                    {{ $result->driver->name ?? 'N/A' }}</td>

                                                {{-- Células Dinâmicas --}}
                                                @if ($reportType === 'official')
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        {{ $result->departure_datetime?->format('d/m H:i') }}</td>
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        {{ $result->arrival_datetime?->format('d/m H:i') }}</td>
                                                    <td class="px-4 py-4 align-top text-sm">{{ $result->destination }}
                                                    </td>
                                                    <td class="px-4 py-4 align-top text-sm font-semibold text-right">
                                                        {{ $result->distance_traveled ?? 'N/A' }}</td>
                                                @else
                                                    {{-- private --}}
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        {{ $result->entry_at?->format('d/m H:i') }}</td>
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        {{ $result->exit_at?->format('d/m H:i') ?? '-' }}</td>
                                                    <td class="px-4 py-4 align-top text-sm">
                                                        {{ $result->entry_reason ?: 'N/A' }}</td>
                                                @endif
                                            </tr>
                                            {{-- Linha Opcional Observações/Passageiros --}}
                                            @if ($reportType === 'official' && ($result->passengers || $result->return_observation))
                                                <tr class="bg-gray-50/50 hover:bg-gray-100">
                                                    <td colspan="6"
                                                        class="px-4 py-1 text-xs italic text-gray-600 border-b border-gray-200">
                                                        @if ($result->passengers)
                                                            <strong>Passag.:</strong> {{ $result->passengers }} <br>
                                                        @endif
                                                        @if ($result->return_observation)
                                                            <strong>Obs.:</strong> {{ $result->return_observation }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="{{ $reportType === 'official' ? 6 : 5 }}"
                                                    class="px-6 py-4 text-center text-gray-500">
                                                    Nenhum registo encontrado para os filtros selecionados.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginação --}}
                            @if ($results->hasPages())
                                <div class="mt-4">
                                    {{ $results->links() }}
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Mensagem inicial ou se não houver resultados --}}
                        <div class="p-6 text-center text-gray-500">
                            A tabela será atualizada automaticamente ao alterar os filtros acima.
                        </div>
                @endif
            </div> {{-- Fim card tabela --}}
        </div> {{-- Fim Seção 2 --}}

    </div>
</div>

{{-- Script Chart.js REMOVIDO --}}
</div>
