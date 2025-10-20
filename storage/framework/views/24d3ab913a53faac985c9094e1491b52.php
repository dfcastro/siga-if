<div>
    
    <div class="mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div
                class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <h2 class="text-xl font-semibold text-gray-800">Análise Rápida</h2>

            </div>
        </div>


        
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gerar Relatório Detalhado</h2>
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">
                    
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
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['startDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                                <input type="date" wire:model="endDate" id="end_date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['endDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label for="vehicle" class="block text-sm font-medium text-gray-700">Veículo</label>
                                
                                <select wire:model.live="selectedVehicle" id="vehicle"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                    <option value="">Todos os Veículos</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicleId => $vehicleDescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vehicleId); ?>"><?php echo e($vehicleDescription); ?></option>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>
                            <div>
                                <label for="driver" class="block text-sm font-medium text-gray-700">Motorista</label>
                                <select wire:model.live="selectedDriver" id="driver"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                                    <option value="">Todos os Motoristas</option>
                                    
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driverId => $driverName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($driverId); ?>"><?php echo e($driverName); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                            </div>
                        </div>

                        
                        <div class="mt-6 flex items-center space-x-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-ifnmg-green text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                                <span wire:loading.remove wire:target="generateReport">Filtrar Tabela</span>
                                <span wire:loading wire:target="generateReport">A Filtrar...</span>
                            </button>
                            <!--[if BLOCK]><![endif]--><?php if($reportType === 'oficial'): ?>
                                <a href="<?php echo e(route('reports.official.pdf', ['vehicle_id' => $selectedVehicle, 'start_date' => $startDate, 'end_date' => $endDate, 'driver_id' => $selectedDriver])); ?>"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 <?php if(empty($selectedVehicle)): ?> opacity-50 cursor-not-allowed <?php endif; ?>"
                                    <?php if(empty($selectedVehicle)): ?> onclick="event.preventDefault(); alert('Por favor, selecione um veículo para gerar o relatório de frota oficial.');" <?php endif; ?>>
                                    Exportar PDF
                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('reports.private.pdf', ['vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver, 'start_date' => $startDate, 'end_date' => $endDate])); ?>"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Exportar PDF
                                </a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($results): ?>
            <div class="mt-8 bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Resultados para: <span
                            class="font-bold text-ifnmg-green"><?php echo e($reportType === 'oficial' ? 'Viagens Oficiais' : 'Entradas Particulares'); ?></span>
                    </h3>
                    <div class="overflow-x-auto border border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <!--[if BLOCK]><![endif]--><?php if($reportType === 'oficial'): ?>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Veículo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Motorista</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Período
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Distância (km)</th>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Veículo
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Motorista</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Entrada/Saída</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Observação</th>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 align-middle">
                                            <div class="text-sm font-semibold">
                                                <?php echo e($result->vehicle->model ?? ($result->vehicle_model ?? 'N/A')); ?></div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo e($result->vehicle->license_plate ?? ($result->license_plate ?? 'N/A')); ?>

                                            </div>
                                        </td>
                                        <td class="px-4 py-4 align-middle text-sm"><?php echo e($result->driver->name ?? 'N/A'); ?>

                                        </td>
                                        <!--[if BLOCK]><![endif]--><?php if($reportType === 'oficial'): ?>
                                            <td class="px-4 py-4 align-middle text-sm">
                                                De
                                                <?php echo e(\Carbon\Carbon::parse($result->departure_datetime)->format('d/m/Y H:i')); ?><br>
                                                Até
                                                <?php echo e(\Carbon\Carbon::parse($result->arrival_datetime)->format('d/m/Y H:i')); ?>

                                            </td>
                                            <td class="px-4 py-4 align-middle text-sm font-semibold">
                                                <?php echo e(number_format($result->arrival_odometer - $result->departure_odometer, 0, ',', '.')); ?>

                                                km</td>
                                        <?php else: ?>
                                            <td class="px-4 py-4 align-middle text-sm">
                                                Entrou:
                                                <?php echo e(\Carbon\Carbon::parse($result->entry_at)->format('d/m/Y H:i')); ?><br>
                                                <!--[if BLOCK]><![endif]--><?php if($result->exit_at): ?>
                                                    Saiu:
                                                    <?php echo e(\Carbon\Carbon::parse($result->exit_at)->format('d/m/Y H:i')); ?>

                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </td>
                                            <td class="px-4 py-4 align-middle text-sm">
                                                <?php echo e($result->entry_reason ?: 'N/A'); ?></td>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum registo
                                            encontrado para os filtros selecionados.</td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($results->hasPages()): ?>
                        <div class="mt-4">
                            <?php echo e($results->links()); ?>

                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <?php $__env->startPush('scripts'); ?>
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
    <?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/reports.blade.php ENDPATH**/ ?>