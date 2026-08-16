<div>
    
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-ifnmg-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Submissão de Relatório Mensal
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Envie os seus registos de entradas e saídas finalizadas para validação da fiscalização.
                    </p>
                </div>

                
                <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                    <label for="report_month" class="text-sm font-semibold text-gray-700 pl-2">Referência:</label>
                    <input type="month" wire:model.live="reportMonth" id="report_month"
                        max="<?php echo e(Carbon\Carbon::now()->subMonth()->format('Y-m')); ?>"
                        class="block w-40 pl-3 pr-2 py-1.5 text-sm border-gray-300 focus:outline-none focus:ring-ifnmg-green focus:border-ifnmg-green rounded-md shadow-sm font-bold text-gray-800"
                        title="Selecione um mês anterior ao atual">
                </div>
            </div>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['reportMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 flex justify-end">
                    <p class="text-red-500 text-xs font-semibold bg-red-50 px-2 py-1 rounded inline-flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <?php echo e($message); ?>

                    </p>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        
        <!--[if BLOCK]><![endif]--><?php if(session()->has('message') || session()->has('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 shadow-sm flex items-center"
                role="alert">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-bold text-sm"><?php echo e(session('message') ?? session('success')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php if(session()->has('error')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 shadow-sm flex items-center"
                role="alert">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Operação não permitida</p>
                    <p class="text-xs mt-0.5"><?php echo e(session('error')); ?></p>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-2 sm:px-6 pt-2">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                <button wire:click.prevent="setSubmissionType('private')"
                    class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 <?php echo e($submissionType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    🛂 Veículos Particulares
                </button>
                <button wire:click.prevent="setSubmissionType('official')"
                    class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 <?php echo e($submissionType === 'official' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                    🚗 Veículos Oficiais
                </button>
            </nav>
        </div>

        
        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200 min-h-[400px]">
            <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200" class="h-full">

                <!--[if BLOCK]><![endif]--><?php if($submissionType === 'private'): ?>
                    
                    
                    
                    <div
                        class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Registros Avulsos</h3>
                            <p class="text-sm text-gray-500">Lista de todas as entradas/saídas finalizadas no mês de
                                <?php echo e(\Carbon\Carbon::parse($reportMonth)->translatedFormat('F/Y')); ?>.</p>
                        </div>
                        <button wire:click="confirmSubmission('private')"
                            class="w-full sm:w-auto inline-flex justify-center items-center rounded-md px-6 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all bg-ifnmg-green hover:bg-green-700 focus:ring-green-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            SUBMETER RELATÓRIO
                        </button>
                    </div>

                    <div class="p-0 sm:p-6">
                        
                        <?php echo $__env->make('livewire.partials.guard-report-private-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <div class="mt-4 px-4 pb-4">
                            <?php echo e($privateEntries->links()); ?>

                        </div>
                    </div>
                <?php else: ?>
                    
                    
                    
                    <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-3 mb-1">
                            <!--[if BLOCK]><![endif]--><?php if($selectedVehicleId): ?>
                                <button wire:click="clearSelectedVehicle"
                                    class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-full transition-colors"
                                    title="Voltar à lista">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <h3 class="text-lg font-semibold text-gray-800">
                                <?php echo e($selectedVehicleId ? 'Detalhes do Relatório' : 'Selecione um Veículo'); ?>

                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 ml-<?php echo e($selectedVehicleId ? '10' : '0'); ?>">
                            <?php echo e($selectedVehicleId ? 'Analise as viagens e insira uma observação se necessário antes de enviar.' : 'Para a frota oficial, o relatório deve ser submetido individualmente por veículo.'); ?>

                        </p>
                    </div>

                    <div class="p-0 sm:p-6">
                        <!--[if BLOCK]><![endif]--><?php if($selectedVehicleId): ?>
                            <?php echo $__env->make('livewire.partials.guard-report-official-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php else: ?>
                            <?php echo $__env->make('livewire.partials.guard-report-official-list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
         <?php $__env->slot('title', null, []); ?> 
            <div class="flex items-center gap-2 text-gray-900">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php echo e($confirmationTitle); ?>

            </div>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('content', null, []); ?> 
            <p class="text-base text-gray-600"><?php echo e($confirmationMessage); ?></p>
            <div class="mt-4 p-3 bg-gray-50 rounded-md border border-gray-100 text-sm text-gray-500">
                <p><strong>Importante:</strong> Após a submissão, os registros deste período ficarão bloqueados para
                    edição até que o fiscal analise.</p>
            </div>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('footer', null, []); ?> 
            <div class="flex flex-col-reverse sm:flex-row w-full sm:w-auto gap-2 sm:gap-3">
                <button wire:click="$set('showConfirmationModal', false)" wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancelar
                </button>
                <button wire:click="executeConfirmedAction" wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-ifnmg-green px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Confirmar Envio
                </button>
            </div>
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
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/guard-report.blade.php ENDPATH**/ ?>