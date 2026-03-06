<div>
    
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            
            <?php echo e($this->isPorteiro ? 'Desempenho do Turno' : 'Visão Global da Operação'); ?>

        </h2>

        <div class="mt-4 sm:mt-0 flex items-center bg-white rounded-md shadow-sm border border-gray-200 px-3 py-1">
            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <input type="date" wire:model.live="selectedDate" class="border-0 focus:ring-0 text-sm font-medium text-gray-700 bg-transparent p-1 cursor-pointer">
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-6">
                <?php echo e($this->isPorteiro ? 'Meu Fluxo de Acesso por Hora' : 'Fluxo Global de Entradas por Hora'); ?>

            </h3>

            
            <div wire:ignore x-data="{}" x-init="() => {
                let chartInstance = null;
                const initialData = <?php echo json_encode($entriesByHourData, 15, 512) ?>;
                const chartCanvas = document.getElementById('entriesByHourChart');
            
                if (Chart.getChart(chartCanvas)) {
                    Chart.getChart(chartCanvas).destroy();
                }
            
                chartInstance = new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0') + 'h'),
                        datasets: [{
                            label: 'Nº de Entradas',
                            data: initialData,
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderRadius: 4, // Bordas arredondadas nas barras (Mais moderno)
                            barThickness: 16 // Espessura fixa
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: '#9ca3af', font: {size: 10} },
                                border: {display: false}, // Oculta a linha sólida lateral
                                grid: { color: '#f3f4f6' }
                            },
                            x: {
                                ticks: { color: '#9ca3af', font: {size: 10} },
                                border: {display: false},
                                grid: { display: false } // Sem grid vertical
                            }
                        },
                        plugins: {
                            legend: { display: false }, // Legenda escondida
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleFont: { size: 13 },
                                bodyFont: { size: 14, weight: 'bold' },
                                padding: 10,
                                cornerRadius: 8
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
                <div style="height: 320px;">
                    <canvas id="entriesByHourChart"></canvas>
                </div>
            </div>
        </div>

        
        <div class="flex flex-col gap-4">

            
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['title' => ''.e($this->isPorteiro ? 'Minhas Entradas Registradas' : 'Total de Acessos Liberados').'','value' => $totalPrivateEntriesToday,'color' => 'ifnmg-green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($this->isPorteiro ? 'Minhas Entradas Registradas' : 'Total de Acessos Liberados').'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalPrivateEntriesToday),'color' => 'ifnmg-green']); ?>
                <svg class="w-8 h-8 text-ifnmg-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['title' => ''.e($this->isPorteiro ? 'Minhas Saídas Autorizadas' : 'Viagens Iniciadas (Oficial)').'','value' => $totalOfficialDeparturesToday,'color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($this->isPorteiro ? 'Minhas Saídas Autorizadas' : 'Viagens Iniciadas (Oficial)').'','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalOfficialDeparturesToday),'color' => 'blue']); ?>
                <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['title' => 'Aguardando Saída (No Pátio)','value' => $vehiclesInYard,'color' => 'orange']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Aguardando Saída (No Pátio)','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vehiclesInYard),'color' => 'orange']); ?>
                <svg class="w-8 h-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5h12.75" />
                </svg>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal8f216e051c231b98198765acd723fb77 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f216e051c231b98198765acd723fb77 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stats-card','data' => ['title' => 'Viaturas Oficiais em Rota','value' => $officialTripsInProgress,'color' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stats-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Viaturas Oficiais em Rota','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($officialTripsInProgress),'color' => 'teal']); ?>
                <svg class="w-8 h-8 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $attributes = $__attributesOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__attributesOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f216e051c231b98198765acd723fb77)): ?>
<?php $component = $__componentOriginal8f216e051c231b98198765acd723fb77; ?>
<?php unset($__componentOriginal8f216e051c231b98198765acd723fb77); ?>
<?php endif; ?>

        </div>
    </div>
</div><?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/dashboard-stats.blade.php ENDPATH**/ ?>