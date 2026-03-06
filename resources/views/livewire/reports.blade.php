<div>
    {{-- CABEÇALHO --}}
    {{-- CABEÇALHO --}}
    <div class="bg-white border-b border-gray-200">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                    Pesquisa e Exportação Avançada
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Filtre o histórico da portaria por mês, condutor ou veículo e exporte para PDF.
                </p>
            </div>

            {{-- BOTÕES DE EXPORTAÇÃO DINÂMICOS --}}
            <div class="flex flex-col sm:items-end w-full md:w-auto mt-2 md:mt-0">
                @if ($reportType === 'official')
                    <a href="{{ route('reports.official.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id]) }}"
                        target="_blank"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ empty($vehicle_id) ? 'opacity-60 cursor-not-allowed hover:bg-blue-600' : 'hover:bg-blue-700' }}"
                        @if (empty($vehicle_id)) onclick="event.preventDefault(); document.getElementById('vehicle_id').focus(); document.getElementById('vehicle_id').classList.add('ring-2', 'ring-red-500', 'border-red-500'); setTimeout(() => document.getElementById('vehicle_id').classList.remove('ring-2', 'ring-red-500', 'border-red-500'), 1500);" title="Ação bloqueada" @endif>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Relatório do Veículo
                    </a>

                    {{-- AVISO VISUAL DE SELEÇÃO OBRIGATÓRIA --}}
                    @if (empty($vehicle_id))
                        <span class="text-xs text-red-500 mt-1.5 font-bold flex items-center animate-pulse">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Obrigatório selecionar uma viatura abaixo
                        </span>
                    @endif
                @else
                    <a href="{{ route('reports.private.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id]) }}"
                        target="_blank"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-ifnmg-green text-white text-sm font-bold rounded-lg hover:bg-green-700 shadow-sm transition-all focus:ring-2 focus:ring-green-500 focus:ring-offset-2 mt-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Mês de Particulares
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Aviso</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        {{-- ABAS DE CATEGORIA --}}
        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-2 sm:px-6 pt-2">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                @if ($canViewOfficial)
                    <button wire:click="$set('reportType', 'official')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $reportType === 'official' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🚗 Base da Frota Oficial
                    </button>
                @endif
                @if ($canViewPrivate)
                    <button wire:click="$set('reportType', 'private')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        🛂 Base de Particulares
                    </button>
                @endif
            </nav>
        </div>

        {{-- PAINEL DE FILTROS HÍBRIDO (Select Nativo x Searchable) --}}
        <div class="bg-gray-50 p-4 sm:p-6 border-b border-x border-gray-200 shadow-sm relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

                {{-- Mês (Comum para ambos) --}}
                <div class="lg:col-span-3">
                    <label for="report_month"
                        class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Mês Base</label>
                    <input type="month" wire:model.live="selectedMonth" id="report_month"
                        max="{{ Carbon\Carbon::now()->subMonthNoOverflow()->format('Y-m') }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('selectedMonth') border-red-500 @enderror">
                    @error('selectedMonth')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                @if ($reportType === 'official')
                    {{-- ========================================== --}}
                    {{-- FILTROS NATIVOS (FROTA OFICIAL E SERVIDORES) --}}
                    {{-- ========================================== --}}
                    <div class="lg:col-span-4">
                        <label for="driver_id"
                            class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Servidor /
                            Condutor</label>
                        <select wire:model.live="driver_id" id="driver_id"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Todos os Servidores --</option>
                            @foreach ($this->officialDrivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label for="vehicle_id"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Viatura
                                Oficial</label>
                            <select wire:model.live="vehicle_id" id="vehicle_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Todas as Viaturas --</option>
                                @foreach ($this->officialVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} -
                                        {{ $vehicle->model }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button wire:click="clearFilters" title="Limpar Filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                @else
                    {{-- ========================================== --}}
                    {{-- FILTROS SEARCHABLE (VISITANTES E PARTICULARES) --}}
                    {{-- ========================================== --}}
                    <div class="lg:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Condutor /
                            Visitante</label>
                        <x-searchable-select model="driver_search" label="" placeholder="Digite o nome..."
                            :results="$driver_results" :selectedText="$driver_selected_text" />
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Veículo
                                Particular</label>
                            <x-searchable-select model="vehicle_search" label=""
                                placeholder="Digite a placa ou modelo..." :results="$vehicle_results" :selectedText="$vehicle_selected_text" />
                        </div>

                        <button wire:click="clearFilters" title="Limpar Filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- LOADER DA TABELA --}}
            <div wire:loading
                wire:target="reportType, selectedMonth, previousPage, nextPage, gotoPage, driver_id, vehicle_id"
                class="absolute inset-0 bg-gray-50 bg-opacity-70 flex items-center justify-center z-10 rounded-b-lg">
                <div
                    class="bg-white px-4 py-2 rounded-full shadow border flex items-center gap-2 text-indigo-600 font-semibold text-sm">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Carregando...
                </div>
            </div>
        </div>

        {{-- TABELA DE RESULTADOS (PREVIEW) --}}
        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200">
            @if ($results && $results->count() > 0)
                <div class="hidden md:block overflow-x-auto min-h-[300px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            @if ($reportType === 'official')
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Veículo Oficial</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Servidor / Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Horários do Registro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Destino / Obs</th>
                                </tr>
                            @else
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Veículo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Horários do Registro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Motivo</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($results as $result)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/D') }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">
                                            {{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-800">{{ $result->driver->name ?? 'N/D' }}</div>
                                    </td>

                                    @if ($reportType === 'official')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="w-8 inline-block text-blue-500 font-bold text-xs">OUT</span>
                                                <span
                                                    class="font-medium">{{ $result->departure_datetime?->format('d/m/y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span
                                                    class="w-8 inline-block text-green-500 font-bold text-xs">IN</span>
                                                <span
                                                    class="font-medium">{{ $result->arrival_datetime?->format('d/m/y H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="font-medium text-gray-900 mb-1">
                                                {{ Str::limit($result->destination, 40) }}</div>
                                            <div class="text-xs text-gray-500 flex flex-wrap gap-2">
                                                <span
                                                    class="bg-gray-100 px-2 py-0.5 rounded border">{{ $result->distance_traveled ?? 'N/A' }}
                                                    km</span>
                                                @if ($result->passengers)
                                                    <span class="bg-gray-100 px-2 py-0.5 rounded border">👤
                                                        {{ $result->passengers }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="w-8 inline-block text-green-500 font-bold text-xs">IN</span>
                                                <span
                                                    class="font-medium">{{ $result->entry_at?->format('d/m/y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span
                                                    class="w-8 inline-block text-red-500 font-bold text-xs">OUT</span>
                                                <span
                                                    class="font-medium">{{ $result->exit_at?->format('d/m/y H:i') ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ Str::limit($result->entry_reason ?: 'N/A', 50) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE PREVIEW CARDS --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach ($results as $result)
                        <div class="p-4 bg-white relative">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1 {{ $reportType === 'private' ? 'bg-green-500' : 'bg-blue-500' }}">
                            </div>
                            <div class="pl-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900 leading-none">
                                            {{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/D') }}</h3>
                                        <p class="text-xs font-mono text-gray-500 mt-1">
                                            {{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D') }}
                                        </p>
                                    </div>
                                    @if ($reportType === 'official')
                                        <span
                                            class="bg-gray-100 text-gray-600 border border-gray-200 text-[10px] font-bold px-2 py-1 rounded">
                                            {{ $result->distance_traveled ?? 'N/A' }} km
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-700 mb-3 space-y-1">
                                    <p>👤 <span class="font-medium">{{ $result->driver->name ?? 'N/D' }}</span></p>
                                    <p class="text-xs text-gray-500 leading-tight">
                                        <span
                                            class="font-semibold">{{ $reportType === 'private' ? 'Motivo:' : 'Destino:' }}</span>
                                        {{ $reportType === 'private' ? Str::limit($result->entry_reason ?: 'N/D', 60) : Str::limit($result->destination, 60) }}
                                    </p>
                                </div>
                                <div
                                    class="bg-gray-50 rounded p-2 flex justify-between text-xs font-medium text-gray-600 border border-gray-100">
                                    @if ($reportType === 'private')
                                        <span><span class="text-green-500 mr-1">IN:</span>
                                            {{ $result->entry_at?->format('d/m H:i') }}</span>
                                        <span><span class="text-red-500 mr-1">OUT:</span>
                                            {{ $result->exit_at?->format('d/m H:i') ?? '-' }}</span>
                                    @else
                                        <span><span class="text-blue-500 mr-1">OUT:</span>
                                            {{ $result->departure_datetime?->format('d/m H:i') }}</span>
                                        <span><span class="text-green-500 mr-1">IN:</span>
                                            {{ $result->arrival_datetime?->format('d/m H:i') ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    {{ $results->links() }}
                </div>
            @else
                <div class="p-12 text-center text-gray-500 bg-gray-50 rounded-b-xl border-dashed border-t">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Nenhum registro finalizado encontrado para este mês e filtros.
                </div>
            @endif
        </div>
    </div>
</div>
