<div>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Meus Relatórios Pendentes')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    
                    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p><?php echo e(session('message')); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php if(session()->has('error')): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p><?php echo e(session('error')); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    
                    <div class="col-span-6 sm:col-span-3">
                        <label for="report_month" class="block font-medium text-sm text-gray-700">Mês do
                            Relatório</label>
                        
                        <input type="month" wire:model.live="reportMonth" id="report_month"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reportMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click.prevent="setSubmissionType('private')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo e($submissionType === 'private' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                                Veículos Particulares
                            </button>
                            <button wire:click.prevent="setSubmissionType('official')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo e($submissionType === 'official' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                                Veículos Oficiais
                            </button>
                        </nav>
                    </div>

                    
                    <div wire:loading.class="opacity-50">
                        <div wire:loading.class="opacity-50">
                            <!--[if BLOCK]><![endif]--><?php if($submissionType === 'private'): ?>
                                <div class="mb-6">
                                    
                                    <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['wire:click' => 'confirmSubmission(\'private\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmSubmission(\'private\')']); ?>
                                        Submeter Relatório de Particulares
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

                                
                                

                                <?php echo $__env->make('livewire.partials.guard-report-private-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <div class="mt-4"><?php echo e($privateEntries->links()); ?></div>
                            <?php else: ?>
                                <!--[if BLOCK]><![endif]--><?php if($selectedVehicleId): ?>
                                    <?php echo $__env->make('livewire.partials.guard-report-official-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php else: ?>
                                    <?php echo $__env->make('livewire.partials.guard-report-official-list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal603c875b7c312212746d277aee5ca6d2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal603c875b7c312212746d277aee5ca6d2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.confirmation-dialog','data' => ['wire:model.live' => 'showConfirmationModal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('confirmation-dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'showConfirmationModal']); ?>
             <?php $__env->slot('title', null, []); ?> <?php echo e($confirmationTitle); ?> <?php $__env->endSlot(); ?>
             <?php $__env->slot('content', null, []); ?> <?php echo e($confirmationMessage); ?> <?php $__env->endSlot(); ?>
             <?php $__env->slot('footer', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => '$set(\'showConfirmationModal\', false)','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'showConfirmationModal\', false)','wire:loading.attr' => 'disabled']); ?>
                    Cancelar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.danger-button','data' => ['class' => 'ms-3','wire:click' => 'executeConfirmedAction','wire:loading.attr' => 'disabled']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('danger-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ms-3','wire:click' => 'executeConfirmedAction','wire:loading.attr' => 'disabled']); ?>
                    Confirmar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $attributes = $__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__attributesOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11)): ?>
<?php $component = $__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11; ?>
<?php unset($__componentOriginal656e8c5ea4d9a4fa173298297bfe3f11); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal603c875b7c312212746d277aee5ca6d2)): ?>
<?php $attributes = $__attributesOriginal603c875b7c312212746d277aee5ca6d2; ?>
<?php unset($__attributesOriginal603c875b7c312212746d277aee5ca6d2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal603c875b7c312212746d277aee5ca6d2)): ?>
<?php $component = $__componentOriginal603c875b7c312212746d277aee5ca6d2; ?>
<?php unset($__componentOriginal603c875b7c312212746d277aee5ca6d2); ?>
<?php endif; ?>

        
        <?php $__env->startPush('scripts'); ?>
            <script>
                document.addEventListener('livewire:initialized', () => {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').on('submit-form', ({
                        formId
                    }) => {
                        const form = document.getElementById(formId);
                        if (form) {
                            form.submit();
                        }
                    });
                });
            </script>
        <?php $__env->stopPush(); ?>
    </div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/guard-report.blade.php ENDPATH**/ ?>