<div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm animate-fade-in">
    
    <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Preparando Relatório para:
                <!--[if BLOCK]><![endif]--><?php if($selectedVehicleEntries->isNotEmpty() && $selectedVehicleEntries->first()->vehicle): ?>
                    <span
                        class="font-bold text-ifnmg-green block sm:inline"><?php echo e($selectedVehicleEntries->first()->vehicle->model); ?>

                        - <?php echo e($selectedVehicleEntries->first()->vehicle->license_plate); ?></span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo e($selectedVehicleEntries->count()); ?> registo(s) encontrado(s) para o período selecionado.
            </p>
            
            <p class="text-sm font-bold text-gray-700 mt-2 block lg:hidden">
                Distância Total: <?php echo e(number_format($totalDistance, 0, ',', '.')); ?> km
            </p>
        </div>
        <button wire:click="clearSelectedVehicle"
            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors mt-2 sm:mt-0">&larr; Voltar
            para a lista</button>
    </div>

    
    <div class="hidden lg:block overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Saída</th>
                    <th class="px-4 py-3">Chegada</th>
                    <th class="px-4 py-3">Condutor</th>
                    <th class="px-4 py-3">Destino</th>
                    <th class="px-4 py-3 text-center">Odômetros (Saída/Chegada)</th>
                    <th class="px-4 py-3 text-center">Distância (KM)</th>
                    
                </tr>
            </thead>
            <tbody class="divide-y">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $selectedVehicleEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php echo e(\Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i')); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php echo e($entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito'); ?>

                        </td>
                        <td class="px-4 py-3"><?php echo e($entry->driver->name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3"><?php echo e($entry->destination); ?></td>
                        <td class="px-4 py-3 text-center font-mono">
                            <?php echo e(number_format($entry->departure_odometer, 0, ',', '.')); ?> /
                            <?php echo e(number_format($entry->arrival_odometer, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-center font-mono font-bold text-gray-700">
                            <?php echo e(number_format($entry->distance_traveled, 0, ',', '.')); ?> km</td>
                        
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum registo para este veículo
                            no período selecionado.</td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
            
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right text-gray-800">Distância Total Percorrida no Período:
                    </td>
                    <td class="px-4 py-3 text-center font-mono text-gray-900">
                        <?php echo e(number_format($totalDistance, 0, ',', '.')); ?> km</td>
                </tr>
            </tfoot>
        </table>
    </div>

    
    <div class="block lg:hidden space-y-4">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $selectedVehicleEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    
                    <div class="col-span-2">
                        <p class="font-semibold text-gray-500">Condutor:</p>
                        <p class="text-gray-800"><?php echo e($entry->driver->name ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-span-2">
                        <p class="font-semibold text-gray-500">Destino:</p>
                        <p class="text-gray-800"><?php echo e($entry->destination); ?></p>
                    </div>
                    
                    <div>
                        <p class="font-semibold text-gray-500">Saída:</p>
                        <p class="text-gray-800">
                            <?php echo e(\Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Chegada:</p>
                        <p class="text-gray-800">
                            <?php echo e($entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito'); ?>

                        </p>
                    </div>
                    
                    <div>
                        <p class="font-semibold text-gray-500">Odômetros:</p>
                        <p class="text-gray-800 font-mono"><?php echo e(number_format($entry->departure_odometer, 0, ',', '.')); ?>

                            / <?php echo e(number_format($entry->arrival_odometer, 0, ',', '.')); ?></p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-500">Distância:</p>
                        <p class="text-gray-800 font-mono font-bold">
                            <?php echo e(number_format($entry->distance_traveled, 0, ',', '.')); ?> km</p>
                    </div>

                    
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-8 px-4 border-2 border-dashed rounded-lg">
                <p class="text-gray-500">Nenhum registo para este veículo no período selecionado.</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
    <div class="mt-6 pt-6 border-t">
        <h4 class="font-semibold text-md text-gray-800">Submeter Relatório para Fiscal</h4>
        <div class="mt-4">
            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'observation','value' => 'Observações Gerais do Relatório (opcional)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observation','value' => 'Observações Gerais do Relatório (opcional)']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $attributes = $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581)): ?>
<?php $component = $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581; ?>
<?php unset($__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581); ?>
<?php endif; ?>
            <textarea wire:model="observation" id="observation" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green"></textarea>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['observation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-sm text-red-600 mt-1"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="mt-6 flex justify-end">
            <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['wire:click' => 'confirmSubmission(\'official\')','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmSubmission(\'official\')','wire:loading.attr' => 'disabled']); ?>
                Submeter Relatório
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/partials/guard-report-official-details.blade.php ENDPATH**/ ?>