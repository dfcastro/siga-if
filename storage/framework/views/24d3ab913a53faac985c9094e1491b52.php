<div>
    
    
    <div class="bg-white border-b border-gray-200">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                    </svg>
                    Pesquisa e Exportação Avançada
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Filtre o histórico da portaria por mês, condutor ou veículo e exporte para PDF.
                </p>
            </div>

            
            <div class="flex flex-col sm:items-end w-full md:w-auto mt-2 md:mt-0">
                <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                    <a href="<?php echo e(route('reports.official.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id])); ?>"
                        target="_blank"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        <?php echo e(empty($vehicle_id) ? 'opacity-60 cursor-not-allowed hover:bg-blue-600' : 'hover:bg-blue-700'); ?>"
                        <?php if(empty($vehicle_id)): ?> onclick="event.preventDefault(); document.getElementById('vehicle_id').focus(); document.getElementById('vehicle_id').classList.add('ring-2', 'ring-red-500', 'border-red-500'); setTimeout(() => document.getElementById('vehicle_id').classList.remove('ring-2', 'ring-red-500', 'border-red-500'), 1500);" title="Ação bloqueada" <?php endif; ?>>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Relatório do Veículo
                    </a>

                    
                    <!--[if BLOCK]><![endif]--><?php if(empty($vehicle_id)): ?>
                        <span class="text-xs text-red-500 mt-1.5 font-bold flex items-center animate-pulse">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Obrigatório selecionar uma viatura abaixo
                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php else: ?>
                    <a href="<?php echo e(route('reports.private.pdf', ['start_date' => $pdfStartDate, 'end_date' => $pdfEndDate, 'vehicle_id' => $vehicle_id, 'driver_id' => $driver_id])); ?>"
                        target="_blank"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-ifnmg-green text-white text-sm font-bold rounded-lg hover:bg-green-700 shadow-sm transition-all focus:ring-2 focus:ring-green-500 focus:ring-offset-2 mt-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Mês de Particulares
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!--[if BLOCK]><![endif]--><?php if(session()->has('error')): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <p class="font-bold">Aviso</p>
                <p class="text-sm"><?php echo e(session('error')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-2 sm:px-6 pt-2">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                <!--[if BLOCK]><![endif]--><?php if($canViewOfficial): ?>
                    <button wire:click="$set('reportType', 'official')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 <?php echo e($reportType === 'official' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        🚗 Base da Frota Oficial
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($canViewPrivate): ?>
                    <button wire:click="$set('reportType', 'private')"
                        class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 <?php echo e($reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        🛂 Base de Particulares
                    </button>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </nav>
        </div>

        
        <div class="bg-gray-50 p-4 sm:p-6 border-b border-x border-gray-200 shadow-sm relative">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

                
                <div class="lg:col-span-3">
                    <label for="report_month"
                        class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Mês Base</label>
                    <input type="month" wire:model.live="selectedMonth" id="report_month"
                        max="<?php echo e(Carbon\Carbon::now()->subMonthNoOverflow()->format('Y-m')); ?>"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php $__errorArgs = ['selectedMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedMonth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                    
                    
                    
                    <div class="lg:col-span-4">
                        <label for="driver_id"
                            class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Servidor /
                            Condutor</label>
                        <select wire:model.live="driver_id" id="driver_id"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">-- Todos os Servidores --</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->officialDrivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($driver->id); ?>"><?php echo e($driver->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label for="vehicle_id"
                                class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Viatura
                                Oficial</label>
                            <select wire:model.live="vehicle_id" id="vehicle_id"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Todas as Viaturas --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->officialVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vehicle->id); ?>"><?php echo e($vehicle->license_plate); ?> -
                                        <?php echo e($vehicle->model); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>

                        <button wire:click="clearFilters" title="Limpar Filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                <?php else: ?>
                    
                    
                    
                    <div class="lg:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Condutor /
                            Visitante</label>
                        <?php if (isset($component)) { $__componentOriginal93f56a9791a1857c74a51e0e80d6e731 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-select','data' => ['model' => 'driver_search','label' => '','placeholder' => 'Digite o nome...','results' => $driver_results,'selectedText' => $driver_selected_text]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'driver_search','label' => '','placeholder' => 'Digite o nome...','results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($driver_results),'selectedText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($driver_selected_text)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $attributes = $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $component = $__componentOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
                    </div>

                    <div class="lg:col-span-5 flex flex-col sm:flex-row gap-2 items-end w-full">
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Veículo
                                Particular</label>
                            <?php if (isset($component)) { $__componentOriginal93f56a9791a1857c74a51e0e80d6e731 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-select','data' => ['model' => 'vehicle_search','label' => '','placeholder' => 'Digite a placa ou modelo...','results' => $vehicle_results,'selectedText' => $vehicle_selected_text]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['model' => 'vehicle_search','label' => '','placeholder' => 'Digite a placa ou modelo...','results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vehicle_results),'selectedText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vehicle_selected_text)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $attributes = $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $component = $__componentOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
                        </div>

                        <button wire:click="clearFilters" title="Limpar Filtros"
                            class="w-full sm:w-auto h-[38px] px-4 bg-white border border-gray-300 rounded-md text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors shadow-sm flex items-center justify-center gap-2 font-semibold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="sm:hidden">Limpar</span>
                        </button>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div wire:loading
                wire:target="reportType, selectedMonth, previousPage, nextPage, gotoPage, driver_id, vehicle_id"
                class="absolute inset-0 bg-gray-50 bg-opacity-70 flex items-center justify-center z-10 rounded-b-lg">
                <div
                    class="bg-white px-4 py-2 rounded-full shadow border flex items-center gap-2 text-indigo-600 font-semibold text-sm">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Carregando...
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200">
            <!--[if BLOCK]><![endif]--><?php if($results && $results->count() > 0): ?>
                <div class="hidden md:block overflow-x-auto min-h-[300px]">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Veículo Oficial</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Servidor / Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Horários do Registro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Destino / Obs</th>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Veículo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Horários do Registro</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Motivo</th>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            <?php echo e($result->vehicle->model ?? ($result->vehicle_model ?? 'N/D')); ?></div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">
                                            <?php echo e($result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D')); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-800"><?php echo e($result->driver->name ?? 'N/D'); ?></div>
                                    </td>

                                    <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="w-8 inline-block text-blue-500 font-bold text-xs">OUT</span>
                                                <span
                                                    class="font-medium"><?php echo e($result->departure_datetime?->format('d/m/y H:i')); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span
                                                    class="w-8 inline-block text-green-500 font-bold text-xs">IN</span>
                                                <span
                                                    class="font-medium"><?php echo e($result->arrival_datetime?->format('d/m/y H:i')); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="font-medium text-gray-900 mb-1">
                                                <?php echo e(Str::limit($result->destination, 40)); ?></div>
                                            <div class="text-xs text-gray-500 flex flex-wrap gap-2">
                                                <span
                                                    class="bg-gray-100 px-2 py-0.5 rounded border"><?php echo e($result->distance_traveled ?? 'N/A'); ?>

                                                    km</span>
                                                <!--[if BLOCK]><![endif]--><?php if($result->passengers): ?>
                                                    <span class="bg-gray-100 px-2 py-0.5 rounded border">👤
                                                        <?php echo e($result->passengers); ?></span>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="w-8 inline-block text-green-500 font-bold text-xs">IN</span>
                                                <span
                                                    class="font-medium"><?php echo e($result->entry_at?->format('d/m/y H:i')); ?></span>
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span
                                                    class="w-8 inline-block text-red-500 font-bold text-xs">OUT</span>
                                                <span
                                                    class="font-medium"><?php echo e($result->exit_at?->format('d/m/y H:i') ?? '-'); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <?php echo e(Str::limit($result->entry_reason ?: 'N/A', 50)); ?>

                                        </td>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                
                <div class="md:hidden divide-y divide-gray-100">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 bg-white relative">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1 <?php echo e($reportType === 'private' ? 'bg-green-500' : 'bg-blue-500'); ?>">
                            </div>
                            <div class="pl-2">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900 leading-none">
                                            <?php echo e($result->vehicle->model ?? ($result->vehicle_model ?? 'N/D')); ?></h3>
                                        <p class="text-xs font-mono text-gray-500 mt-1">
                                            <?php echo e($result->vehicle->license_plate ?? ($result->license_plate ?? 'N/D')); ?>

                                        </p>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php if($reportType === 'official'): ?>
                                        <span
                                            class="bg-gray-100 text-gray-600 border border-gray-200 text-[10px] font-bold px-2 py-1 rounded">
                                            <?php echo e($result->distance_traveled ?? 'N/A'); ?> km
                                        </span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="text-sm text-gray-700 mb-3 space-y-1">
                                    <p>👤 <span class="font-medium"><?php echo e($result->driver->name ?? 'N/D'); ?></span></p>
                                    <p class="text-xs text-gray-500 leading-tight">
                                        <span
                                            class="font-semibold"><?php echo e($reportType === 'private' ? 'Motivo:' : 'Destino:'); ?></span>
                                        <?php echo e($reportType === 'private' ? Str::limit($result->entry_reason ?: 'N/D', 60) : Str::limit($result->destination, 60)); ?>

                                    </p>
                                </div>
                                <div
                                    class="bg-gray-50 rounded p-2 flex justify-between text-xs font-medium text-gray-600 border border-gray-100">
                                    <!--[if BLOCK]><![endif]--><?php if($reportType === 'private'): ?>
                                        <span><span class="text-green-500 mr-1">IN:</span>
                                            <?php echo e($result->entry_at?->format('d/m H:i')); ?></span>
                                        <span><span class="text-red-500 mr-1">OUT:</span>
                                            <?php echo e($result->exit_at?->format('d/m H:i') ?? '-'); ?></span>
                                    <?php else: ?>
                                        <span><span class="text-blue-500 mr-1">OUT:</span>
                                            <?php echo e($result->departure_datetime?->format('d/m H:i')); ?></span>
                                        <span><span class="text-green-500 mr-1">IN:</span>
                                            <?php echo e($result->arrival_datetime?->format('d/m H:i') ?? '-'); ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <?php echo e($results->links()); ?>

                </div>
            <?php else: ?>
                <div class="p-12 text-center text-gray-500 bg-gray-50 rounded-b-xl border-dashed border-t">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Nenhum registro finalizado encontrado para este mês e filtros.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/reports.blade.php ENDPATH**/ ?>