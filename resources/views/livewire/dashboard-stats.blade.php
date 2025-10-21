<div>
    {{-- Título e seletor de data --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 ">Painel de Controle</h2>

        <input type="date" wire:model.live="selectedDate"
            class="mt-4 sm:mt-0 border-gray-300  rounded-md shadow-sm focus:border-ifnmg-green focus:ring focus:ring-ifnmg-green focus:ring-opacity-50">
    </div>

    {{-- Container Principal do Conteúdo --}}
    {{-- Layout de 1 coluna em telas pequenas, e 3 colunas em telas grandes (lg) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- COLUNA ESQUERDA (GRÁFICO) - Ocupa 2 das 3 colunas em telas grandes --}}
        <div class="lg:col-span-2 bg-white  p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-700  mb-4">Fluxo de Entradas por Hora</h3>

            {{-- Bloco do Gráfico com Alpine.js --}}
            <div wire:ignore x-data="{}" x-init="() => {
                let chartInstance = null;
                const initialData = @json($entriesByHourData);
                const chartCanvas = document.getElementById('entriesByHourChart');
            
                if (Chart.getChart(chartCanvas)) {
                    Chart.getChart(chartCanvas).destroy();
                }
            
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
                                ticks: { stepSize: 1, color: '#6b7280' }, // Cor dos ticks do eixo Y
                                grid: { color: 'rgba(209, 213, 219, 0.4)' } // Cor das linhas do grid
                            },
                            x: {
                                ticks: { color: '#6b7280' }, // Cor dos ticks do eixo X
                                grid: { display: false } // Oculta o grid vertical
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#6b7280' } // Cor da legenda
                            }
                        }
                    }
                });
            
                Livewire.on('updateChartData', (payload) => {
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

        {{-- COLUNA DIREITA (CARDS DE ESTATÍSTICAS) --}}
        <div class="flex flex-col gap-8">

            {{-- NOVO Card de Entradas Particulares --}}
            <x-stats-card title="Entradas Particulares ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m') }})"
                :value="$totalPrivateEntriesToday" color="ifnmg-green">
                {{-- Ícone de Entrada ou Calendário --}}
                <svg class="w-8 h-8 text-ifnmg-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </x-stats-card>

            {{-- NOVO Card de Saídas Oficiais --}}
            <x-stats-card title="Saídas Oficiais ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m') }})"
                :value="$totalOfficialDeparturesToday" color="teal"> {{-- Usei a cor 'teal' como sugestão --}}
                {{-- Ícone de Saída --}}
                <svg class="w-8 h-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </x-stats-card>

            {{-- Card de Veículos no Pátio (mantém-se) --}}
            <x-stats-card title="Veículos Particulares no Pátio" :value="$vehiclesInYard" color="orange">
                <svg class="w-8 h-8 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5h12.75" />
                </svg>
            </x-stats-card>

            {{-- Card de Viagens Oficiais (mantém-se) --}}
            <x-stats-card title="Viagens Oficiais em Andamento" :value="$officialTripsInProgress" color="blue">
                <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-stats-card>

            {{-- Nota: Agora temos 4 cards nesta coluna. Verifique se o layout fica bom em telas menores. Se necessário, podemos ajustar o grid geral. --}}
        </div>
    </div>
</div>
