<div>
    
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-ifnmg-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Painel de Relatórios Mensais
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Acompanhe o status e a aprovação das submissões do ano selecionado.
                </p>
            </div>

            
            <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                <label for="year_filter" class="text-sm font-semibold text-gray-700 pl-2">Ano Exercício:</label>
                <select wire:model.live="selectedYear" id="year_filter"
                    class="block w-32 pl-3 pr-10 py-1.5 text-base border-gray-300 focus:outline-none focus:ring-ifnmg-green focus:border-ifnmg-green sm:text-sm rounded-md shadow-sm font-bold text-gray-800"
                    aria-label="Selecionar Ano">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option><?php echo e(Carbon\Carbon::now()->year); ?></option>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
        </div>
    </div>

    <div class="bg-gray-100/50 p-6 lg:p-8 min-h-screen">

        <?php
            $user = Auth::user();
            $canSeeOfficial = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['official', 'both']);
            $canSeePrivate = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['private', 'both']);

            // CORREÇÃO: Porteiros agora podem ver as abas normalmente, pois têm acesso a ambas as categorias!
            $showTabs = $canSeeOfficial && $canSeePrivate;
        ?>

        <!--[if BLOCK]><![endif]--><?php if($showTabs): ?>
            <div class="border-b border-gray-200 mb-6 bg-white rounded-t-lg shadow-sm px-4 pt-2">
                <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    <button wire:click="$set('reportType', 'official')"
                        class="<?php echo e($reportType === 'official' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-2 border-b-2 font-bold text-sm transition-colors flex items-center gap-2">
                        🚗 Frota Oficial
                    </button>
                    <button wire:click="$set('reportType', 'private')"
                        class="<?php echo e($reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-2 border-b-2 font-bold text-sm transition-colors flex items-center gap-2">
                        🛂 Entradas Particulares
                    </button>
                </nav>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div>
            
            
            
            <!--[if BLOCK]><![endif]--><?php if($reportType === 'official' && $canSeeOfficial): ?>
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <?php echo e($user->role === 'porteiro' ? 'Meus Relatórios da Frota Oficial' : 'Relatórios da Frota Oficial'); ?>

                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        <?php echo e($user->role === 'porteiro'
                            ? "Acompanhe a aprovação das suas submissões para o ano de {$selectedYear}."
                            : "Exibindo o status de submissão por veículo para o ano de {$selectedYear}."); ?>

                    </p>
                </div>

                <div class="space-y-6">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-shadow hover:shadow-md">
                            <div
                                class="bg-gray-50/80 px-5 py-3 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                    <?php echo e($vehicle->model); ?>

                                    <span
                                        class="bg-white border border-gray-200 text-gray-600 text-xs font-mono px-2 py-1 rounded shadow-sm"><?php echo e($vehicle->license_plate); ?></span>
                                </h3>
                            </div>
                            <div class="p-5">
                                <?php echo $__env->make('livewire.partials.status-grid', [
                                    'months' => $months,
                                    'submissionsMap' => $submissions[$vehicle->id] ?? [],
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div
                            class="py-12 px-6 text-center text-gray-500 bg-white rounded-xl shadow-sm border border-gray-100 border-dashed">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Nenhum veículo oficial com registros encontrado para este ano.
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                
                
            <?php elseif($reportType === 'private' && $canSeePrivate): ?>
                <!--[if BLOCK]><![endif]--><?php if($user->role === 'porteiro'): ?>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-blue-50/50 px-5 py-4 border-b border-blue-100">
                            <h3 class="font-bold text-blue-900 text-lg flex items-center gap-2">
                                👨‍✈️ Meus Relatórios Submetidos (Particulares)
                            </h3>
                            <p class="text-xs text-blue-600 mt-1">Acompanhe a aprovação dos seus registros pela
                                fiscalização.</p>
                        </div>
                        <div class="p-5">
                            <?php echo $__env->make('livewire.partials.status-grid', [
                                'months' => $months,
                                'submissionsMap' => $submissions,
                            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Relatórios de Veículos Particulares</h2>
                        <p class="mt-1 text-sm text-gray-600">Exibindo o status de submissão por porteiro para o ano de
                            <strong><?php echo e($selectedYear); ?></strong>.</p>
                    </div>

                    <div class="space-y-6">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $porteiros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $porteiro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div
                                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-shadow hover:shadow-md">
                                <div class="bg-gray-50/80 px-5 py-3 border-b border-gray-100">
                                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                        <div
                                            class="h-8 w-8 rounded-full bg-ifnmg-green text-white flex items-center justify-center font-bold text-sm">
                                            <?php echo e(substr($porteiro->name, 0, 1)); ?>

                                        </div>
                                        <?php echo e($porteiro->name); ?>

                                    </h3>
                                </div>
                                <div class="p-5">
                                    <?php echo $__env->make('livewire.partials.status-grid', [
                                        'months' => $months,
                                        'submissionsMap' => $submissions[$porteiro->id] ?? [],
                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div
                                class="py-12 px-6 text-center text-gray-500 bg-white rounded-xl shadow-sm border border-gray-100 border-dashed">
                                Nenhum porteiro cadastrado no sistema.
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/report-status.blade.php ENDPATH**/ ?>