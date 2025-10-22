<div>
    
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-medium text-gray-900">
            Status de Submissão de Relatórios
        </h1>
        <p class="mt-2 text-gray-600">
            Acompanhe aqui o status dos relatórios mensais submetidos.
        </p>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        <div class="mb-4 flex justify-end">
            <div>
                <label for="year_filter" class="text-sm font-medium text-gray-700 sr-only">Ano:</label>
                <select wire:model.live="selectedYear" id="year_filter"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-ifnmg-green focus:border-ifnmg-green sm:text-sm rounded-md shadow-sm"
                    aria-label="Selecionar Ano">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option><?php echo e(Carbon::now()->year); ?></option>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
        </div>
        <?php
            // Lógica para decidir se as abas devem ser exibidas
            $user = Auth::user();
            $canSeeOfficial = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['official', 'both']);
            $canSeePrivate = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['private', 'both']);

            // ### ALTERAÇÃO: Porteiros não veem abas, vão direto para seus relatórios ###
            $showTabs = $canSeeOfficial && $canSeePrivate && $user->role !== 'porteiro';
        ?>

        <!--[if BLOCK]><![endif]--><?php if($showTabs): ?>
            <div class="border-b border-gray-200 mb-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="$set('reportType', 'official')"
                        class="<?php echo e($reportType === 'official' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Relatórios de Veículos Oficiais
                    </button>
                    <button wire:click="$set('reportType', 'private')"
                        class="<?php echo e($reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Relatórios de Veículos Particulares
                    </button>
                </nav>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <div>
            
            <div class="mb-6 pb-4 border-b border-gray-200">
                <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Relatórios da Frota Oficial
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Exibindo o status de submissão por veículo para o ano de <strong><?php echo e($selectedYear); ?></strong>.
                    </p>
                <?php elseif($reportType === 'private'): ?>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Relatórios de Veículos Particulares
                    </h2>
                    <!--[if BLOCK]><![endif]--><?php if(Auth::user()->role === 'porteiro'): ?>
                        <p class="mt-1 text-sm text-gray-600">
                            Exibindo o status das suas submissões para o ano de <strong><?php echo e($selectedYear); ?></strong>.
                        </p>
                    <?php else: ?>
                        <p class="mt-1 text-sm text-gray-600">
                            Exibindo o status de submissão por porteiro para o ano de
                            <strong><?php echo e($selectedYear); ?></strong>.
                        </p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            
            <!--[if BLOCK]><![endif]--><?php if($reportType === 'official' && $canSeeOfficial && $user->role !== 'porteiro'): ?>
                <div class="space-y-4">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <h3 class="font-bold text-gray-800"><?php echo e($vehicle->model); ?>

                                    (<?php echo e($vehicle->license_plate); ?>)
                                </h3>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $monthKey = $month->format('Y-m');
                                        $submission = $submissions[$vehicle->id][$monthKey] ?? null;
                                        $status = $submission->status ?? 'not_submitted';

                                        $statusClasses = [
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        ];
                                        $statusLabels = [
                                            'approved' => 'Aprovado',
                                            'pending' => 'Pendente',
                                            'rejected' => 'Reprovado',
                                            'not_submitted' => 'Não Enviado',
                                        ];
                                    ?>
                                    <div class="border rounded-md p-3 text-center <?php echo e($statusClasses[$status]); ?>">
                                        <div class="font-semibold text-sm"><?php echo e($month->translatedFormat('M/y')); ?></div>
                                        <div class="text-xs mt-1"><?php echo e($statusLabels[$status]); ?></div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="py-4 px-6 text-center text-gray-500 bg-white rounded-lg shadow">Nenhum veículo
                            oficial para exibir.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
            <?php elseif($reportType === 'private' && $canSeePrivate): ?>
                

                
                <!--[if BLOCK]><![endif]--><?php if($user->role === 'porteiro'): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gray-50 p-4 border-b">
                            <h3 class="font-bold text-gray-800">
                                Seus Relatórios de Veículos Particulares
                            </h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $monthKey = $month->format('Y-m');
                                    $submission = $submissions[$monthKey] ?? null;
                                    $status = $submission->status ?? 'not_submitted';
                                    $statusClasses = [
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    ];
                                    $statusLabels = [
                                        'approved' => 'Aprovado',
                                        'pending' => 'Pendente',
                                        'rejected' => 'Reprovado',
                                        'not_submitted' => 'Não Enviado',
                                    ];
                                ?>
                                <div class="border rounded-md p-3 text-center <?php echo e($statusClasses[$status]); ?>">
                                    <div class="font-semibold text-sm"><?php echo e($month->translatedFormat('M/y')); ?></div>
                                    <div class="text-xs mt-1"><?php echo e($statusLabels[$status]); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                <?php else: ?>
                    <div class="space-y-4">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $porteiros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $porteiro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b">
                                    <h3 class="font-bold text-gray-800">
                                        Porteiro: <?php echo e($porteiro->name); ?>

                                    </h3>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $monthKey = $month->format('Y-m');
                                            // Busca a submissão para este porteiro específico
                                            $submission = $submissions[$porteiro->id][$monthKey] ?? null;
                                            $status = $submission->status ?? 'not_submitted';

                                            $statusClasses = [
                                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                                'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            ];
                                            $statusLabels = [
                                                'approved' => 'Aprovado',
                                                'pending' => 'Pendente',
                                                'rejected' => 'Reprovado',
                                                'not_submitted' => 'Não Enviado',
                                            ];
                                        ?>
                                        <div class="border rounded-md p-3 text-center <?php echo e($statusClasses[$status]); ?>">
                                            <div class="font-semibold text-sm"><?php echo e($month->translatedFormat('M/y')); ?>

                                            </div>
                                            <div class="text-xs mt-1"><?php echo e($statusLabels[$status]); ?></div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="py-4 px-6 text-center text-gray-500 bg-white rounded-lg shadow">
                                Nenhum porteiro encontrado no sistema.
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                

            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/report-status.blade.php ENDPATH**/ ?>