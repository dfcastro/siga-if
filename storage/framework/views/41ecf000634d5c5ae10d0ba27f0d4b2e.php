<div>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Aprovação e Arquivo de Relatórios')); ?>

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

                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click.prevent="setFilter('pending')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo e($filterStatus === 'pending' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                                Pendentes
                            </button>
                            <button wire:click.prevent="setFilter('approved')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm <?php echo e($filterStatus === 'approved' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                                Aprovados (Arquivo)
                            </button>
                        </nav>
                    </div>

                    <div class="shadow-md sm:rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
                                <tr>
                                    <th class="px-6 py-3">Porteiro</th>
                                    <th class="px-6 py-3">Período</th>
                                    <!--[if BLOCK]><![endif]--><?php if($filterStatus === 'pending'): ?>
                                        <th class="px-6 py-3">Submissão</th>
                                    <?php else: ?>
                                        <th class="px-6 py-3">Aprovado Por</th>
                                        <th class="px-6 py-3">Aprovação</th>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <th class="px-6 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="space-y-4 sm:space-y-0">
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr
                                        class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                                        <td
                                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                            <span class="font-bold text-gray-600 sm:hidden">Porteiro</span> <span
                                                class="text-right font-medium text-gray-900"><?php echo e($submission->guardUser?->name ?? 'Usuário Removido'); ?></span>
                                        </td>
                                        <td
                                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                            <span class="font-bold text-gray-600 sm:hidden">Período</span> <span
                                                class="text-right"><?php echo e($submission->start_date->format('d/m/Y')); ?> a
                                                <?php echo e($submission->end_date->format('d/m/Y')); ?></span></td>
                                        <!--[if BLOCK]><![endif]--><?php if($filterStatus === 'pending'): ?>
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Submissão</span> <span
                                                    class="text-right"><?php echo e($submission->submitted_at->format('d/m/Y H:i')); ?></span>
                                            </td>
                                        <?php else: ?>
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Aprovado Por</span>
                                                <span
                                                    class="text-right"><?php echo e($submission->fiscal?->name ?? 'N/A'); ?></span>
                                            </td>
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Aprovação</span> <span
                                                    class="text-right"><?php echo e($submission->approved_at?->format('d/m/Y H:i') ?? 'N/A'); ?></span>
                                            </td>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <td class="pt-3 sm:pt-0 sm:py-4 sm:px-6 sm:table-cell"><?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['class' => 'w-full sm:w-auto','wire:click' => 'viewSubmission('.e($submission->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full sm:w-auto','wire:click' => 'viewSubmission('.e($submission->id).')']); ?>Ver
                                                Detalhes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="bg-white sm:border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum relatório
                                            <?php echo e($filterStatus === 'pending' ? 'pendente' : 'aprovado'); ?> encontrado.
                                        </td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4"><?php echo e($submissions->links()); ?></div>

                </div>
            </div>
        </div>
    </div>

    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['wire:model.defer' => 'showDetailsModal','maxWidth' => '7xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.defer' => 'showDetailsModal','maxWidth' => '7xl']); ?>
        <!--[if BLOCK]><![endif]--><?php if($selectedSubmission): ?>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Detalhes do Relatório</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Porteiro:</strong> <?php echo e($selectedSubmission->guardUser?->name ?? 'Usuário Removido'); ?></p>
                    <p><strong>Período:</strong> <?php echo e($selectedSubmission->start_date->format('d/m/Y')); ?> a
                        <?php echo e($selectedSubmission->end_date->format('d/m/Y')); ?></p>
                    <!--[if BLOCK]><![endif]--><?php if($selectedSubmission->status === 'approved'): ?>
                        <p class="mt-2 text-green-700"><strong>Aprovado por:</strong>
                            <?php echo e($selectedSubmission->fiscal?->name ?? 'N/A'); ?> em
                            <?php echo e($selectedSubmission->approved_at?->format('d/m/Y H:i')); ?></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <?php $privateEntries = $submissionEntries->whereInstanceOf(\App\Models\PrivateEntry::class); ?>
                <!--[if BLOCK]><![endif]--><?php if($privateEntries->isNotEmpty()): ?>
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-blue-50 rounded-t-lg border-b">Registos
                            de Veículos Particulares:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[70vh] overflow-y-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2">Veículo</th>
                                        <th class="px-4 py-2">Condutor</th>
                                        <th class="px-4 py-2">Período (Entrada → Saída)</th>
                                        <th class="px-4 py-2">Motivo</th>
                                        <th class="px-4 py-2">Porteiro (Saída)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $privateEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="<?php echo e($loop->even ? 'bg-gray-50' : ''); ?>">
                                            <td class="px-4 py-2 font-medium"><?php echo e($entry->vehicle?->model ?? 'N/A'); ?>

                                                (<?php echo e($entry->vehicle?->license_plate ?? ''); ?>)</td>
                                            <td class="px-4 py-2"><?php echo e($entry->driver?->name ?? 'N/A'); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($entry->entry_at?->format('d/m H:i')); ?> →
                                                <?php echo e($entry->exit_at?->format('d/m H:i') ?? '(No pátio)'); ?></td>
                                            <td class="px-4 py-2"><?php echo e($entry->entry_reason); ?></td>
                                            <td class="px-4 py-2"><?php echo e($entry->guard_on_exit ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <?php $officialTrips = $submissionEntries->whereInstanceOf(\App\Models\OfficialTrip::class); ?>
                <!--[if BLOCK]><![endif]--><?php if($officialTrips->isNotEmpty()): ?>
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-green-50 rounded-t-lg border-b">
                            Registos de Veículos Oficiais:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[70vh] overflow-y-auto">
                            <table class="w-full text-sm text-left text-gray-500" style="table-layout: fixed;">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 w-[15%]">Veículo</th>
                                        <th class="px-4 py-2 w-[15%]">Condutor</th>
                                        <th class="px-4 py-2 w-[20%]">Passageiros</th>
                                        <th class="px-4 py-2 w-[20%]">Período (Saída → Chegada)</th>
                                        <th class="px-4 py-2 w-[15%]">KM (S/C/R)</th>
                                        <th class="px-4 py-2 w-[15%]">Destino</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $officialTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="<?php echo e($loop->even ? 'bg-gray-50' : ''); ?>">
                                            <td class="px-4 py-2 font-medium whitespace-nowrap">
                                                <?php echo e($trip->vehicle?->model ?? 'N/A'); ?>

                                                (<?php echo e($trip->vehicle?->license_plate ?? ''); ?>)</td>
                                            <td class="px-4 py-2 whitespace-nowrap"><?php echo e($trip->driver?->name ?? 'N/A'); ?>

                                            </td>
                                            <td class="px-4 py-2 break-words"><?php echo e($trip->passengers ?: 'N/A'); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($trip->departure_datetime?->format('d/m H:i')); ?> →
                                                <?php echo e($trip->arrival_datetime?->format('d/m H:i') ?? '(Em Viagem)'); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap"><?php echo e($trip->departure_odometer); ?> /
                                                <?php echo e($trip->arrival_odometer); ?> / <span
                                                    class="font-bold"><?php echo e($trip->arrival_datetime ? $trip->arrival_odometer - $trip->departure_odometer : 'N/A'); ?></span>
                                            </td>
                                            <td class="px-4 py-2 break-words"><?php echo e($trip->destination); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-xs text-gray-500 mt-2 px-1">
                            <strong>* Legenda KM (S/C/R):</strong> Saída / Chegada / Rodado
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="mt-6 flex justify-end space-x-4">
                    <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => 'cancelView']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'cancelView']); ?>Fechar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                    <!--[if BLOCK]><![endif]--><?php if($selectedSubmission?->status === 'pending'): ?>
                        <?php if (isset($component)) { $__componentOriginald411d1792bd6cc877d687758b753742c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald411d1792bd6cc877d687758b753742c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['wire:click' => 'approveSubmission','wire:confirm' => 'Tem a certeza que deseja aprovar este relatório?']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'approveSubmission','wire:confirm' => 'Tem a certeza que deseja aprovar este relatório?']); ?>Dar Visto e
                            Arquivar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $attributes = $__attributesOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__attributesOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald411d1792bd6cc877d687758b753742c)): ?>
<?php $component = $__componentOriginald411d1792bd6cc877d687758b753742c; ?>
<?php unset($__componentOriginald411d1792bd6cc877d687758b753742c); ?>
<?php endif; ?>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/fiscal-approval.blade.php ENDPATH**/ ?>