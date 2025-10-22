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
                    <?php if(session()->has('error')): ?>
                        
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p><?php echo e(session('error')); ?></p>
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

                    
                    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->role === 'admin' || auth()->user()->fiscal_type === 'both'): ?>
                        <div class="mb-4 flex space-x-4 text-sm"> 
                            <button wire:click.prevent="setTypeFilter('')"
                                class="<?php echo e($typeFilter === '' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600'); ?> transition duration-150 ease-in-out">Todos</button>
                            <button wire:click.prevent="setTypeFilter('official')"
                                class="<?php echo e($typeFilter === 'official' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600'); ?> transition duration-150 ease-in-out">Apenas
                                Oficiais</button>
                            <button wire:click.prevent="setTypeFilter('private')"
                                class="<?php echo e($typeFilter === 'private' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600'); ?> transition duration-150 ease-in-out">Apenas
                                Particulares</button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <div class="shadow-sm border border-gray-200 sm:rounded-lg overflow-hidden"> 
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
                                <tr>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Porteiro</th>
                                    <th class="px-6 py-3">Período</th>
                                    
                                    <!--[if BLOCK]><![endif]--><?php if($filterStatus === 'pending'): ?>
                                        <th class="px-6 py-3">Submetido em</th>
                                    <?php else: ?>
                                        
                                        <th class="px-6 py-3">Aprovado Por</th>
                                        <th class="px-6 py-3">Data Aprovação</th>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <th class="px-6 py-3 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 sm:divide-y-0"> 
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr
                                        class="bg-white block sm:table-row mb-4 sm:mb-0 border sm:border-0 rounded-lg sm:rounded-none shadow-sm sm:shadow-none">
                                        

                                        
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Tipo:</span>
                                            <span class="text-right">
                                                <!--[if BLOCK]><![endif]--><?php if($submission->type === 'official'): ?>
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oficial</span>
                                                    <div class="text-xs text-gray-500 sm:hidden mt-1">
                                                        <?php echo e($submission->vehicle?->model); ?>

                                                        (<?php echo e($submission->vehicle?->license_plate); ?>)</div>
                                                    
                                                <?php else: ?>
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Particular</span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </span>
                                        </td>

                                        
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Porteiro:</span>
                                            <span
                                                class="text-right font-medium text-gray-900"><?php echo e($submission->guardUser?->name ?? 'Usuário Removido'); ?></span>
                                        </td>

                                        
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Período:</span>
                                            <span
                                                class="text-right"><?php echo e($submission->start_date->format('M/Y')); ?></span>
                                            
                                        </td>

                                        
                                        <!--[if BLOCK]><![endif]--><?php if($filterStatus === 'pending'): ?>
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Submetido:</span>
                                                <span
                                                    class="text-right text-xs"><?php echo e($submission->submitted_at->diffForHumans()); ?></span>
                                                
                                            </td>
                                        <?php else: ?>
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Aprovado
                                                    Por:</span>
                                                <span
                                                    class="text-right"><?php echo e($submission->fiscal?->name ?? 'N/A'); ?></span>
                                            </td>
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Aprovação:</span>
                                                <span
                                                    class="text-right text-xs"><?php echo e($submission->approved_at?->diffForHumans() ?? 'N/A'); ?></span>
                                                
                                            </td>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <td class="px-4 py-3 sm:px-6 sm:py-4 sm:table-cell text-center sm:text-left">
                                            
                                            <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['class' => 'w-full sm:w-auto','wire:click' => 'viewSubmission('.e($submission->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full sm:w-auto','wire:click' => 'viewSubmission('.e($submission->id).')']); ?>
                                                Ver Detalhes
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
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="bg-white">
                                        <td colspan="<?php echo e($filterStatus === 'pending' ? '5' : '6'); ?>"
                                            class="px-6 py-4 text-center text-gray-500"> 
                                            Nenhum relatório
                                            <?php echo e($filterStatus === 'pending' ? 'pendente' : 'aprovado'); ?> encontrado para
                                            os filtros selecionados.
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
                <h2 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Relatório</h2>

                
                <div class="mb-6 pb-4 border-b border-gray-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span><strong>Tipo:</strong></span>
                        <!--[if BLOCK]><![endif]--><?php if($selectedSubmission->type === 'official'): ?>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oficial</span>
                        <?php else: ?>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Particular</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($selectedSubmission->type === 'official' && $selectedSubmission->vehicle): ?>
                        <div class="flex justify-between"><span><strong>Veículo:</strong></span>
                            <span><?php echo e($selectedSubmission->vehicle->model); ?>

                                (<?php echo e($selectedSubmission->vehicle->license_plate); ?>)</span></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="flex justify-between"><span><strong>Porteiro:</strong></span>
                        <span><?php echo e($selectedSubmission->guardUser?->name ?? 'Usuário Removido'); ?></span></div>
                    <div class="flex justify-between"><span><strong>Período:</strong></span>
                        <span><?php echo e($selectedSubmission->start_date->format('d/m/Y')); ?> a
                            <?php echo e($selectedSubmission->end_date->format('d/m/Y')); ?></span></div>
                    <div class="flex justify-between"><span><strong>Submetido em:</strong></span>
                        <span><?php echo e($selectedSubmission->submitted_at->format('d/m/Y H:i')); ?></span></div>
                    <!--[if BLOCK]><![endif]--><?php if($selectedSubmission->status === 'approved'): ?>
                        <div class="flex justify-between text-green-700"><span><strong>Aprovado por:</strong></span>
                            <span><?php echo e($selectedSubmission->fiscal?->name ?? 'N/A'); ?> em
                                <?php echo e($selectedSubmission->approved_at?->format('d/m/Y H:i')); ?></span></div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($selectedSubmission->type === 'official' && $selectedSubmission->observation): ?>
                        <div class="pt-2"><strong>Observação do Porteiro:</strong>
                            <p class="text-gray-600 italic bg-gray-50 p-2 rounded border mt-1">
                                <?php echo e($selectedSubmission->observation); ?></p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <?php $privateEntries = $submissionEntries->whereInstanceOf(\App\Models\PrivateEntry::class); ?>
                <!--[if BLOCK]><![endif]--><?php if($privateEntries->isNotEmpty()): ?>
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-blue-50 rounded-t-lg border-b">Registos
                            de Veículos Particulares:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[60vh] overflow-y-auto border">
                            
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    
                                    <tr>
                                        <th class="px-4 py-2">Veículo (Placa)</th>
                                        <th class="px-4 py-2">Condutor</th>
                                        <th class="px-4 py-2">Entrada</th>
                                        <th class="px-4 py-2">Saída</th>
                                        <th class="px-4 py-2">Motivo</th>
                                        <th class="px-4 py-2">Porteiro (Saída)</th> 
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $privateEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50"> 
                                            <td class="px-4 py-2 font-medium">
                                                <?php echo e($entry->vehicle_model ?? 'N/A'); ?> <span
                                                    class="font-mono">(<?php echo e($entry->license_plate ?? ''); ?>)</span>
                                            </td>
                                            <td class="px-4 py-2"><?php echo e($entry->driver?->name ?? 'N/A'); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($entry->entry_at?->format('d/m H:i')); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($entry->exit_at?->format('d/m H:i') ?? '-'); ?></td>
                                            <td class="px-4 py-2"><?php echo e($entry->entry_reason); ?></td>
                                            
                                            <td class="px-4 py-2"><?php echo e($entry->guardExit?->name ?? 'N/A'); ?></td>
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
                            Registos de Viagens Oficiais:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[60vh] overflow-y-auto border">
                            
                            <table class="w-full text-sm text-left text-gray-500" style="table-layout: fixed;">
                                
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    
                                    <tr>
                                        
                                        <th class="px-4 py-2 w-[18%]">Condutor</th>
                                        <th class="px-4 py-2 w-[22%]">Passageiros</th>
                                        <th class="px-4 py-2 w-[18%]">Saída</th>
                                        <th class="px-4 py-2 w-[18%]">Chegada</th>
                                        <th class="px-4 py-2 w-[10%] text-right">KM Rodado</th>
                                        <th class="px-4 py-2 w-[14%]">Destino</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $officialTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50"> 
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($trip->driver?->name ?? 'N/A'); ?></td>
                                            <td class="px-4 py-2 break-words"><?php echo e($trip->passengers ?: '-'); ?></td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($trip->departure_datetime?->format('d/m H:i')); ?>

                                                (<?php echo e($trip->departure_odometer); ?> km)</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                <?php echo e($trip->arrival_datetime?->format('d/m H:i')); ?>

                                                (<?php echo e($trip->arrival_odometer); ?> km)</td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                <?php echo e($trip->distance_traveled ?? 'N/A'); ?></td>
                                            <td class="px-4 py-2 break-words"><?php echo e($trip->destination); ?></td>
                                            
                                            
                                            
                                        </tr>
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($trip->return_observation): ?>
                                            <tr class="bg-gray-50/50">
                                                <td colspan="6" class="px-4 py-1 text-xs italic text-gray-600">
                                                    <strong>Obs. Retorno:</strong> <?php echo e($trip->return_observation); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                                <tfoot class="bg-gray-100 font-bold sticky bottom-0"> 
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right text-gray-800 uppercase">
                                            Distância Total Rodada:</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-900">
                                            <?php echo e(number_format($totalDistance, 0, ',', '.')); ?></td>
                                        <td class="px-4 py-3">km</td>
                                    </tr>
                                </tfoot>
                            </table>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['wire:click' => 'approveSubmission','wire:confirm' => 'Tem a certeza que deseja aprovar este relatório?','wire:loading.attr' => 'disabled','wire:target' => 'approveSubmission']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'approveSubmission','wire:confirm' => 'Tem a certeza que deseja aprovar este relatório?','wire:loading.attr' => 'disabled','wire:target' => 'approveSubmission']); ?>
                            <span wire:loading wire:target="approveSubmission">Aprovando...</span>
                            
                            <span wire:loading.remove wire:target="approveSubmission">Dar Visto e Arquivar</span>
                            
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