<div>
    {{-- SEÇÃO 1: DASHBOARD DE ANALYTICS VISUAL --}}
    <div class="mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div
                class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <h2 class="text-xl font-semibold text-gray-800">Análise Rápida</h2>

            </div>
        </div>
       

    {{-- SEÇÃO 2: RELATÓRIOS DETALHADOS (COM LÓGICA CORRIGIDA) --}}
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gerar Relatório Detalhado</h2>
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6">
                {{-- O wire:submit agora funcionará --}}
                <form wire:submit="generateReport">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-4 items-end">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select wire:model.live="reportType" id="report_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                <option value="oficial">Viagens Oficiais</option>
                                <option value="particular">Entradas Particulares</option>
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data de
                                Início</label>
                            <input type="date" wire:model="startDate" id="start_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                            @error('startDate')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                            <input type="date" wire:model="endDate" id="end_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                            @error('endDate')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="vehicle" class="block text-sm font-medium text-gray-700">Veículo</label>
                            {{-- Este select agora usa a propriedade computada 'vehicles' --}}
                            <select wire:model.live="selectedVehicle" id="vehicle"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                <option value="">Todos os Veículos</option>
                                @foreach ($this->vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->model }}
                                        ({{ $vehicle->license_plate }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="driver" class="block text-sm font-medium text-gray-700">Motorista</label>
                            <select wire:model.live="selectedDriver" id="driver"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                <option value="">Todos os Motoristas</option>
                                @foreach ($this->drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Botões de Ação --}}
                    <div class="mt-6 flex items-center space-x-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-ifnmg-green text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                            <span wire:loading.remove wire:target="generateReport">Filtrar Tabela</span>
                            <span wire:loading wire:target="generateReport">A Filtrar...</span>
                        </button>
                        @if ($reportType === 'oficial')
                            <a href="{{ route('reports.official.pdf', ['vehicle_id' => $selectedVehicle, 'start_date' => $startDate, 'end_date' => $endDate, 'driver_id' => $selectedDriver]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 @if (empty($selectedVehicle)) opacity-50 cursor-not-allowed @endif"
                                @if (empty($selectedVehicle)) onclick="event.preventDefault(); alert('Por favor, selecione um veículo para gerar o relatório de frota oficial.');" @endif>
                                Exportar PDF
                            </a>
                        @else
                            <a href="{{ route('reports.private.pdf', ['vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Exportar PDF
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabela de Resultados Detalhados --}}
    @if ($results)
        <div class="mt-8 bg-white overflow-hidden shadow-md sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resultados para: <span
                        class="font-bold text-ifnmg-green">{{ $reportType === 'oficial' ? 'Viagens Oficiais' : 'Entradas Particulares' }}</span>
                </h3>
                <div class="overflow-x-auto border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            @if ($reportType === 'oficial')
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Veículo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                        Motorista</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Período
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                        Distância (km)</th>
                                </tr>
                            @else
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Veículo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                        Motorista</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                        Entrada/Saída</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                        Observação</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($results as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 align-middle">
                                        <div class="text-sm font-semibold">
                                            {{ $result->vehicle->model ?? ($result->vehicle_model ?? 'N/A') }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $result->vehicle->license_plate ?? ($result->license_plate ?? 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm">{{ $result->driver->name ?? 'N/A' }}
                                    </td>
                                    @if ($reportType === 'oficial')
                                        <td class="px-4 py-4 align-middle text-sm">
                                            De
                                            {{ \Carbon\Carbon::parse($result->departure_datetime)->format('d/m/Y H:i') }}<br>
                                            Até
                                            {{ \Carbon\Carbon::parse($result->arrival_datetime)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-4 align-middle text-sm font-semibold">
                                            {{ number_format($result->arrival_odometer - $result->departure_odometer, 0, ',', '.') }}
                                            km</td>
                                    @else
                                        <td class="px-4 py-4 align-middle text-sm">
                                            Entrou:
                                            {{ \Carbon\Carbon::parse($result->entry_at)->format('d/m/Y H:i') }}<br>
                                            @if ($result->exit_at)
                                                Saiu:
                                                {{ \Carbon\Carbon::parse($result->exit_at)->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 align-middle text-sm">
                                            {{ $result->entry_reason ?: 'N/A' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum registo
                                        encontrado para os filtros selecionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($results->hasPages())
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        function chartData() {
            return {
                chart: null,
                initChart(data) {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    const ctx = document.getElementById('entriesByHourChart').getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Nº de Entradas',
                                data: data.data,
                                backgroundColor: 'rgba(34, 139, 34, 0.6)',
                                borderColor: 'rgba(34, 139, 34, 1)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                },
                updateChart(data) {
                    if (this.chart) {
                        this.chart.data.labels = data.labels;
                        this.chart.data.datasets[0].data = data.data;
                        this.chart.update();
                    }
                }
            }
        }
    </script>
@endpush
