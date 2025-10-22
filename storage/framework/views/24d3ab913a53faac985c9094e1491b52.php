<div>
    
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e($header ?? 'Relatórios Gerenciais'); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            

            
            <div>
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                    <div class="p-6">
                        
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-4">Gerar Relatórios e Filtrar
                            Dados da Tabela</h2>

                        
                        <!--[if BLOCK]><![endif]--><?php if(session()->has('error')): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded"
                                role="alert">
                                <p><?php echo e(session('error')); ?></p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 items-end">
                            
                            
                            <div>
                                <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo
                                    Relatório/Tabela</label>
                                <select wire:model.live="reportType" id="report_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="official">Viagens Oficiais</option>
                                    <option value="private">Entradas Particulares</option> 
                                </select>
                            </div>

                            
                            <div>
                                <label for="report_month" class="block text-sm font-medium text-gray-700">Mês</label>
                                <input type="month" wire:model.live="selectedMonth" id="report_month"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm <?php $__errorArgs = ['selectedMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    max="<?php echo e(Carbon\Carbon::now()->subMonthNoOverflow()->format('Y-m')); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <div>
                                <label for="vehicle_report" class="block text-sm font-medium text-gray-700">Veículo
                                    (Opcional)</label>
                                <select wire:model.live="selectedVehicle" id="vehicle_report"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="">-- Todos --</option>
                                    
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicleId => $vehicleDescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vehicleId); ?>"><?php echo e($vehicleDescription); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedVehicle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <div>
                                <label for="driver_report" class="block text-sm font-medium text-gray-700">Motorista
                                    (Opcional)</label>
                                <select wire:model.live="selectedDriver" id="driver_report"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green sm:text-sm">
                                    <option value="">-- Todos --</option>
                                    
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driverId => $driverName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($driverId); ?>"><?php echo e($driverName); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedDriver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div class="mt-6 flex items-center justify-end space-x-4 border-t pt-4"> 
                            

                            
                            
                            <a href="<?php echo e(route('reports.official.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver])); ?>"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium <?php echo e($reportType !== 'official' ? 'hidden' : ''); ?> <?php if($reportType === 'official' && empty($selectedVehicle)): ?> opacity-50 cursor-not-allowed <?php endif; ?>"
                                <?php if($reportType === 'official' && empty($selectedVehicle)): ?> onclick="event.preventDefault(); alert('Por favor, selecione um VEÍCULO OFICIAL específico para gerar este PDF.');"
                                   title="Selecione um veículo oficial para habilitar" <?php endif; ?>
                                wire:loading.class="opacity-50 cursor-wait" wire:target="render">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Exportar PDF Oficial (por Veículo)
                            </a>

                            
                            
                            <a href="<?php echo e(route('reports.private.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $selectedVehicle, 'driver_id' => $selectedDriver])); ?>"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium <?php echo e($reportType !== 'private' ? 'hidden' : ''); ?>"
                                 wire:loading.class="opacity-50 cursor-wait" wire:target="render">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Exportar PDF Particular
                            </a>
                        </div>
                        
                    </div> 
                </div> 

                
                <!--[if BLOCK]><![endif]--><?php if($results): ?>
                    <div class="mt-8 bg-white overflow-hidden shadow-md sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Registos Concluídos para: <span
                                    class="font-bold text-ifnmg-green"><?php echo e($reportType === 'official' ? 'Viagens Oficiais' : 'Entradas Particulares'); ?></span>
                                
                                <span class="text-sm font-normal text-gray-600">(Mês:
                                    <?php echo e(\Carbon\Carbon::parse($selectedMonth)->translatedFormat('F/Y')); ?>)</span>
                            </h3>
                            <div class="overflow-x-auto border border-gray-200 sm:rounded-lg shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
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
                                        <?php else: ?>
                                            
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
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr class="hover:bg-gray-50">
                                                
                                                <td class="px-4 py-4 align-top">
                                                    <div class="text-sm font-semibold">
                                                        <?php echo e($result->vehicle->model ?? ($result->vehicle_model ?? 'N/A')); ?>

                                                    </div>
                                                    <div class="text-xs text-gray-500 font-mono">
                                                        <?php echo e($result->vehicle->license_plate ?? ($result->license_plate ?? 'N/A')); ?>

                                                    </div>
                                                </td>
                                                
                                                <td class="px-4 py-4 align-top text-sm">
                                                    <?php echo e($result->driver->name ?? 'N/A'); ?></td>

                                                
                                                <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        <?php echo e($result->departure_datetime?->format('d/m H:i')); ?></td>
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        <?php echo e($result->arrival_datetime?->format('d/m H:i')); ?></td>
                                                    <td class="px-4 py-4 align-top text-sm"><?php echo e($result->destination); ?>

                                                    </td>
                                                    <td class="px-4 py-4 align-top text-sm font-semibold text-right">
                                                        <?php echo e($result->distance_traveled ?? 'N/A'); ?></td>
                                                <?php else: ?>
                                                    
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        <?php echo e($result->entry_at?->format('d/m H:i')); ?></td>
                                                    <td class="px-4 py-4 align-top text-xs whitespace-nowrap">
                                                        <?php echo e($result->exit_at?->format('d/m H:i') ?? '-'); ?></td>
                                                    <td class="px-4 py-4 align-top text-sm">
                                                        <?php echo e($result->entry_reason ?: 'N/A'); ?></td>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tr>
                                            
                                            <!--[if BLOCK]><![endif]--><?php if($reportType === 'official' && ($result->passengers || $result->return_observation)): ?>
                                                <tr class="bg-gray-50/50 hover:bg-gray-100">
                                                    <td colspan="6"
                                                        class="px-4 py-1 text-xs italic text-gray-600 border-b border-gray-200">
                                                        <!--[if BLOCK]><![endif]--><?php if($result->passengers): ?>
                                                            <strong>Passag.:</strong> <?php echo e($result->passengers); ?> <br>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                        <!--[if BLOCK]><![endif]--><?php if($result->return_observation): ?>
                                                            <strong>Obs.:</strong> <?php echo e($result->return_observation); ?>

                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                </tr>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="<?php echo e($reportType === 'official' ? 6 : 5); ?>"
                                                    class="px-6 py-4 text-center text-gray-500">
                                                    Nenhum registo encontrado para os filtros selecionados.
                                                </td>
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
                    <?php else: ?>
                        
                        <div class="p-6 text-center text-gray-500">
                            A tabela será atualizada automaticamente ao alterar os filtros acima.
                        </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div> 
        </div> 

    </div>
</div>


</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/reports.blade.php ENDPATH**/ ?>