<div>
    {{-- Título e seletor de data --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Painel de Controle</h2>

        {{-- Este input está corretamente ligado à propriedade $selectedDate no back-end --}}
        <input type="date" wire:model.live="selectedDate"
            class="border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green mt-4 sm:mt-0">
    </div>

    {{-- SEÇÃO DE ESTATÍSTICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Card de Total de Entradas --}}
        <x-stats-card title="Total de Entradas ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m') }})"
            :value="$totalEntriesToday" color="ifnmg-green">
            <svg class="w-6 h-6 text-ifnmg-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </x-stats-card>

        {{-- Card de Veículos no Pátio --}}
        <x-stats-card title="Veículos Particulares no Pátio" :value="$vehiclesInYard" color="orange">
            <svg class="w-6 h-6 text-orange-600" xmlns="http://www.w.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5h12.75" />
            </svg>
        </x-stats-card>

        {{-- Card de Viagens Oficiais --}}
        <x-stats-card title="Viagens Oficiais em Andamento" :value="$officialTripsInProgress" color="blue">
            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-stats-card>
    </div>

    <div class="border-t border-gray-200 my-8"></div>

    {{-- SEÇÃO DE GRÁFICOS --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Fluxo de Entradas por Hora</h3>

        {{-- SEÇÃO DE GRÁFICOS --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-700 mb-4">Fluxo de Entradas por Hora</h3>

            {{-- A mágica acontece aqui. Note que x-data agora está vazio! --}}
            <div wire:ignore x-data="{}" x-init="() => {
                // NÃO vamos usar 'this.chart'. Usaremos uma variável local simples.
                let chartInstance = null;
            
                // Pega os dados iniciais do PHP.
                const initialData = @json($entriesByHourData);
                const chartCanvas = document.getElementById('entriesByHourChart');
            
                // Limpa qualquer gráfico anterior que possa existir.
                if (Chart.getChart(chartCanvas)) {
                    Chart.getChart(chartCanvas).destroy();
                }
            
                // Cria o gráfico e o atribui à nossa variável local, fora da reatividade.
                chartInstance = new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0') + ':00'),
                        datasets: [{
                            label: 'Nº de Entradas',
                            data: initialData,
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                            borderColor: 'rgba(22, 163, 74, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            
                // Ouve o evento do Livewire.
                // Graças ao 'closure' do JavaScript, ele ainda tem acesso à 'chartInstance'.
                Livewire.on('updateChartData', (payload) => {
                    // Verifica se a nossa instância local do gráfico existe.
                    if (chartInstance) {
                        chartInstance.data.datasets[0].data = payload.data;
                        chartInstance.update();
                    }
                });
            }">
                <div style="height: 350px;">
                    <canvas id="entriesByHourChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
