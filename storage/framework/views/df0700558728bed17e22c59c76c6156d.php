<div>
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
            <tr>
                <th class="px-6 py-3">Veículo Oficial</th>
                <th class="px-6 py-3 text-center">Registos Pendentes</th>
                <th class="px-6 py-3">Ações</th>
            </tr>
        </thead>
        <tbody class="space-y-4 sm:space-y-0">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $vehiclesWithOfficialTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                
                <!--[if BLOCK]><![endif]--><?php if(isset($item['vehicle'])): ?>
                    <tr
                        class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Veículo</span>
                            
                            <span class="text-right"><?php echo e($item['vehicle']->model); ?>

                                <span class="block text-xs"><?php echo e($item['vehicle']->license_plate); ?></span>
                            </span>
                        </td>
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell text-left sm:text-center border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Registos Pendentes</span>
                            <span class="text-right"><?php echo e($item['count']); ?></span>
                        </td>
                        <td class="pt-3 sm:py-4 sm:px-6 sm:table-cell">
                            
                            <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['class' => 'w-full sm:w-auto','wire:click' => 'selectVehicle('.e($item['vehicle']->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full sm:w-auto','wire:click' => 'selectVehicle('.e($item['vehicle']->id).')']); ?>Preparar Relatório <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white sm:border-b">
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum veículo oficial com registos
                        pendentes.</td>
                </tr>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </tbody>
    </table>

    
    <div class="mt-4">
        <!--[if BLOCK]><![endif]--><?php if($officialTrips): ?>
            <?php echo e($officialTrips->links()); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div><?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/partials/guard-report-official-list.blade.php ENDPATH**/ ?>