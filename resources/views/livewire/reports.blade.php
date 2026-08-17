<div>
    {{-- CABEÇALHO --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                    Consulta de Movimentações
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Consulte a rotina da portaria por hoje, data específica ou mês, inclusive movimentações ainda abertas.
                </p>
            </div>

            <div class="flex flex-col sm:items-end w-full md:w-auto mt-2 md:mt-0">
                <a href="{{ $reportType === 'official'
                    ? route('reports.official.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id])
                    : route('reports.private.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id]) }}"
                    target="_blank"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 {{ $reportType === 'official' ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' : 'bg-ifnmg-green hover:bg-green-700 focus:ring-green-500' }} text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exportar consulta em PDF
                </a>
                <span class="text-xs text-gray-500 mt-1.5">Período: {{ $periodLabel }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            <strong>Consulta operacional:</strong> esta tela é somente para conferência e pesquisa. O fechamento mensal e a submissão para visto continuam separados.
        </div>

        {{-- ABAS DE CATEGORIA --}}
        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-2 sm:px-6 pt-2">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                @if ($canViewOfficial)
                    <button wire:click="$set('reportType', 'official')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $reportType === 'official' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🚗 Frota Oficial
                    </button>
                @endif
                @if ($canViewPrivate)
                    <button wire:click="$set('reportType', 'private')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🛂 Veículos Particulares
                    </button>
                @endif
            </nav>
        </div>

        {{-- FILTROS --}}
        <div class="bg-gray-50 p-4 sm:p-6 border-b border-x border-gray-200 shadow-sm relative">
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Período da consulta</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="setPeriodMode('today')"
                        class="px-4 py-2 rounded-lg border text-sm font-semibold transition-colors {{ $periodMode === 'today' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}">
                        Hoje
                    </button>
                    <button type="button" wire:click="setPeriodMode('date')"
                        class="px-4 py-2 rounded-lg border text-sm font-semibold transition-colors {{ $periodMode === 'date' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}">
                        Data específica
                    </button>
                    <button type="button" wire:click="setPeriodMode('month')"
                        class="px-4 py-2 rounded-lg border text-sm font-semibold transition-colors {{ $periodMode === 'month' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' }}">
                        Mês
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                <div class="lg:col-span-3">
                    @if ($periodMode === 'month')
                        <label for="report_month" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Mês</label>
                        <input type="month" wire:model.live="selectedMonth" id="report_month"
                            max="{{ Carbon\Carbon::today()->format('Y-m') }}"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('selectedMonth') border-red-500 @enderror">
                        @error('selectedMonth')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    @elseif ($periodMode === 'date')
                        <label for="report_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Data</label>
                        <input type="date" wire:model.live="selectedDate" id="report_date"
                            max="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('selectedDate') border-red-500 @enderror">
                        @error('selectedDate')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    @else
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Data</label>
                        <div class="h-[38px] flex items-center px-3 rounded-md border border-indigo-200 bg-indigo-50 text-sm font-semibold text-indigo-800">
                            {{ Carbon\Carbon::today()->format('d/m/Y') }}
                        </div>
                    @endif
                </div>

                @if ($reportType === 'official')
                    <div class="lg:col-span-4">
                        <label for="driver_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Servidor / Condutor</label>
                        <select wire:model.live="driver_id" id="driver_id"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Todos os Condutores --</option>
                            @foreach ($this->officialDrivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label for="vehicle_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Viatura Oficial</label>
                            <select wire:model.live="vehicle_id" id="vehicle_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Todas as Viaturas --</option>
                                @foreach ($this->officialVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} - {{ $vehicle->model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" wire:click="clearFilters" title="Limpar filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                @else
                    <div class="lg:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Motorista / Condutor</label>
                        <x-searchable-select model="driver_search" label="" placeholder="Digite o nome..."
                            :results="$driver_results" :selectedText="$driver_selected_text" />
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Veículo Particular</label>
                            <x-searchable-select model="vehicle_search" label="" placeholder="Digite placa ou modelo..."
                                :results="$vehicle_results" :selectedText="$vehicle_selected_text" />
                        </div>
                        <button type="button" wire:click="clearFilters" title="Limpar filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                @endif
            </div>

            <div wire:loading
                wire:target="reportType,periodMode,selectedDate,selectedMonth,previousPage,nextPage,gotoPage,driver_id,vehicle_id,setPeriodMode,clearFilters"
                class="absolute inset-0 bg-gray-50 bg-opacity-70 flex items-center justify-center z-10 rounded-b-lg">
                <div class="bg-white px-4 py-2 rounded-full shadow border flex items-center gap-2 text-indigo-600 font-semibold text-sm">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Consultando...
                </div>
            </div>
        </div>

        {{-- RESUMO --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-5">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $reportType === 'private' ? 'Entradas no período' : 'Saídas da frota no período' }}</div>
                <div class="mt-1 text-3xl font-black text-gray-900">{{ $summary['total'] }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">Finalizadas</div>
                <div class="mt-1 text-3xl font-black text-green-700">{{ $summary['completed'] }}</div>
            </div>
            <div class="bg-white rounded-xl border {{ $summary['open'] > 0 ? 'border-amber-300' : 'border-gray-200' }} shadow-sm p-4">
                <div class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $reportType === 'private' ? 'No pátio / campus' : 'Em viagem' }}</div>
                <div class="mt-1 text-3xl font-black {{ $summary['open'] > 0 ? 'text-amber-700' : 'text-gray-700' }}">{{ $summary['open'] }}</div>
            </div>
        </div>

        {{-- RESULTADOS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                <div>
                    <h2 class="font-bold text-gray-900">Movimentações encontradas</h2>
                    <p class="text-xs text-gray-500">{{ $periodLabel }}</p>
                </div>
                <span class="text-xs font-semibold text-gray-500">{{ $results->total() }} registro(s)</span>
            </div>

            @if ($results->count() > 0)
                <div class="hidden md:block overflow-x-auto min-h-[300px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Veículo</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motorista</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Horários / Porteiros</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $reportType === 'private' ? 'Motivo' : 'Destino / Informações' }}</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($results as $result)
                                @php
                                    $isOpen = $reportType === 'private' ? is_null($result->exit_at) : is_null($result->arrival_datetime);
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/D') }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D') }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-800 font-medium">{{ $result->driver->name ?? 'N/D' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 min-w-[220px]">
                                        @if ($reportType === 'private')
                                            <div><span class="font-bold text-green-600">IN</span> {{ $result->entry_at?->format('d/m/y H:i') }} <span class="text-xs text-gray-400">• {{ $result->guardEntry?->name ?? 'N/D' }}</span></div>
                                            <div class="mt-1"><span class="font-bold text-red-600">OUT</span> {{ $result->exit_at?->format('d/m/y H:i') ?? '-' }} <span class="text-xs text-gray-400">• {{ $result->guardExit?->name ?? '-' }}</span></div>
                                        @else
                                            <div><span class="font-bold text-blue-600">OUT</span> {{ $result->departure_datetime?->format('d/m/y H:i') }} <span class="text-xs text-gray-400">• {{ $result->guardDeparture?->name ?? 'N/D' }}</span></div>
                                            <div class="mt-1"><span class="font-bold text-green-600">IN</span> {{ $result->arrival_datetime?->format('d/m/y H:i') ?? '-' }} <span class="text-xs text-gray-400">• {{ $result->guardArrival?->name ?? '-' }}</span></div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 max-w-md">
                                        @if ($reportType === 'private')
                                            {{ Str::limit($result->entry_reason ?: 'N/D', 80) }}
                                        @else
                                            <div class="font-medium text-gray-900">{{ Str::limit($result->destination ?: 'N/D', 70) }}</div>
                                            <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-2">
                                                @if (!$isOpen)
                                                    <span>{{ $result->distance_traveled ?? 0 }} km</span>
                                                @endif
                                                @if ($result->passengers)
                                                    <span>• Passageiros: {{ Str::limit($result->passengers, 40) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if ($isOpen)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                {{ $reportType === 'private' ? 'No pátio' : 'Em viagem' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">Finalizado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach ($results as $result)
                        @php
                            $isOpen = $reportType === 'private' ? is_null($result->exit_at) : is_null($result->arrival_datetime);
                        @endphp
                        <div class="p-4 bg-white relative">
                            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isOpen ? 'bg-amber-500' : ($reportType === 'private' ? 'bg-green-500' : 'bg-blue-500') }}"></div>
                            <div class="pl-2">
                                <div class="flex justify-between items-start gap-3 mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/D') }}</h3>
                                        <p class="text-xs font-mono text-gray-500 mt-1">{{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D') }}</p>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-1 rounded border {{ $isOpen ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-green-100 text-green-800 border-green-200' }}">
                                        {{ $isOpen ? ($reportType === 'private' ? 'NO PÁTIO' : 'EM VIAGEM') : 'FINALIZADO' }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-800 mb-2">👤 <span class="font-medium">{{ $result->driver->name ?? 'N/D' }}</span></p>
                                <p class="text-xs text-gray-500 mb-3">
                                    <span class="font-semibold">{{ $reportType === 'private' ? 'Motivo:' : 'Destino:' }}</span>
                                    {{ $reportType === 'private' ? Str::limit($result->entry_reason ?: 'N/D', 70) : Str::limit($result->destination ?: 'N/D', 70) }}
                                </p>

                                <div class="bg-gray-50 rounded p-2 text-xs text-gray-600 border border-gray-100 space-y-1">
                                    @if ($reportType === 'private')
                                        <div><span class="font-bold text-green-600">IN:</span> {{ $result->entry_at?->format('d/m H:i') }} • {{ $result->guardEntry?->name ?? 'N/D' }}</div>
                                        <div><span class="font-bold text-red-600">OUT:</span> {{ $result->exit_at?->format('d/m H:i') ?? '-' }} • {{ $result->guardExit?->name ?? '-' }}</div>
                                    @else
                                        <div><span class="font-bold text-blue-600">OUT:</span> {{ $result->departure_datetime?->format('d/m H:i') }} • {{ $result->guardDeparture?->name ?? 'N/D' }}</div>
                                        <div><span class="font-bold text-green-600">IN:</span> {{ $result->arrival_datetime?->format('d/m H:i') ?? '-' }} • {{ $result->guardArrival?->name ?? '-' }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $results->links() }}
                </div>
            @else
                <div class="p-12 text-center text-gray-500 bg-gray-50">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Nenhuma movimentação encontrada para o período e filtros selecionados.
                </div>
            @endif
        </div>
    </div>
</div>
