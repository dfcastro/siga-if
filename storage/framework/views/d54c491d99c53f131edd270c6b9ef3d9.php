<div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Preparando Relatório para:
                <span class="font-bold text-indigo-600"><?php echo e($selectedVehicleEntries->first()->vehicle->model); ?> -
                    <?php echo e($selectedVehicleEntries->first()->vehicle->license_plate); ?></span>
            </h3>
            <p class="text-sm text-gray-500">
                <?php echo e($selectedVehicleEntries->count()); ?> registo(s) encontrado(s) para o período selecionado.
            </p>
        </div>
        <button wire:click="clearSelectedVehicle" class="text-sm text-gray-600 hover:text-gray-900">&larr; Voltar para a
            lista</button>
    </div>

    
    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Data/Hora da Saída</th>
                    <th class="px-6 py-3">Condutor</th>
                    <th class="px-6 py-3">Destino</th>
                    <th class="px-6 py-3">Data/Hora da Chegada</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $selectedVehicleEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <?php echo e(\Carbon\Carbon::parse($entry->departure_datetime)->format('d/m/Y H:i')); ?></td>
                        <td class="px-6 py-4"><?php echo e($entry->driver->name ?? 'N/A'); ?></td>
                        <td class="px-6 py-4"><?php echo e($entry->destination); ?></td>
                        <td class="px-6 py-4">
                            <?php echo e($entry->arrival_datetime ? \Carbon\Carbon::parse($entry->arrival_datetime)->format('d/m/Y H:i') : 'Em trânsito'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum registo para este veículo
                            no período selecionado.</td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    
    <div class="mt-6 pt-6 border-t">
        <h4 class="font-semibold text-md text-gray-800">Submeter Relatório para Fiscal</h4>
        <div class="mt-4">
            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'observation','value' => 'Observações (opcional)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observation','value' => 'Observações (opcional)']); ?>
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
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
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
                <div wire:loading wire:target="confirmSubmission('official')"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5" />
                    </svg>
                </div>
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