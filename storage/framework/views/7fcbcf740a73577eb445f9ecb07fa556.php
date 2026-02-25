<div x-data="{ tab: 'entrada' }">
    
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-4 shadow-sm"
            role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold text-sm">Sucesso!</p>
                    <p class="text-xs"><?php echo e($successMessage); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->role !== 'fiscal'): ?>

        
        <div class="bg-white shadow-sm sm:rounded-t-lg border-b border-gray-200 mb-4 sm:mb-6">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                <button @click="tab = 'entrada'"
                    :class="tab === 'entrada' ? 'border-ifnmg-green text-ifnmg-green' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm sm:text-base transition-colors duration-200">
                    📥 Nova Entrada
                </button>
                <button @click="tab = 'saida'"
                    :class="tab === 'saida' ? 'border-ifnmg-green text-ifnmg-green' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm sm:text-base transition-colors duration-200 flex items-center justify-center gap-2">
                    📤 Pátio / Saídas
                    <!--[if BLOCK]><![endif]--><?php if(count($currentVehicles) > 0): ?>
                        <span
                            class="bg-gray-100 text-gray-800 py-0.5 px-2.5 rounded-full text-xs font-bold"><?php echo e(count($currentVehicles)); ?></span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </button>
            </nav>
        </div>

        <div>
            
            
            
            <div x-show="tab === 'entrada'" x-transition.opacity.duration.300ms
                class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg sm:rounded-t-none rounded-lg border border-gray-100">
                <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-semibold text-gray-800">Registrar Entrada</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Busque pelo veículo ou preencha manualmente.</p>
                </div>

                <form wire:submit="save" class="p-4 sm:p-6">
                    <div class="space-y-5 sm:space-y-6">

                        
                        <div class="relative">
                            <label for="search" class="block text-sm font-medium text-gray-700">Busca Rápida</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="search" wire:model.live.debounce.500ms="search"
                                    placeholder="Placa, modelo, nome ou CPF..."
                                    class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-sm sm:text-base">
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if(strlen($search) >= 3): ?>
                                <div
                                    class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    <!--[if BLOCK]><![endif]--><?php if(!empty($searchResults) && count($searchResults) > 0): ?>
                                        <ul class="divide-y divide-gray-100">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="px-4 py-3 cursor-pointer hover:bg-green-50 text-sm transition-colors"
                                                    wire:click="selectVehicle(<?php echo e($result['id']); ?>)">
                                                    <?php echo e($result['text']); ?>

                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </ul>
                                    <?php else: ?>
                                        <p class="px-4 py-3 text-sm text-gray-500 text-center">Nenhum resultado
                                            encontrado.</p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 pt-4 border-t border-gray-100">
                            <div x-data="{
                                plate: $wire.entangle('license_plate').live,
                                formatPlate() {
                                    let clean = String(this.plate || '').toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 7);
                                    let formatted = '';
                                    for (let i = 0; i < clean.length; i++) {
                                        const char = clean[i];
                                        if (i <= 2 && /[A-Z]/.test(char)) formatted += char;
                                        else if (i === 3 && /[0-9]/.test(char)) formatted += char;
                                        else if (i === 4 && /[A-Z0-9]/.test(char)) formatted += char;
                                        else if (i >= 5 && /[0-9]/.test(char)) formatted += char;
                                    }
                                    if (/^[A-Z]{3}[0-9]{4}$/.test(formatted)) {
                                        formatted = formatted.substring(0, 3) + '-' + formatted.substring(3);
                                    }
                                    this.plate = formatted;
                                }
                            }" x-init="$watch('plate', () => formatPlate())">
                                <label for="license_plate" class="block text-sm font-medium text-gray-700">Placa</label>
                                <input type="text" id="license_plate" x-model="plate"
                                    placeholder="AAA-1234 ou ABC1D23"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green font-mono uppercase text-sm sm:text-base <?php $__errorArgs = ['license_plate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['license_plate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div>
                                <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Modelo do
                                    Veículo</label>
                                <input type="text" id="vehicle_model" wire:model.live="vehicle_model"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-sm sm:text-base <?php $__errorArgs = ['vehicle_model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['vehicle_model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div class="bg-gray-50/50 p-3 sm:p-4 rounded-lg border border-gray-100">
                            <div class="flex justify-between items-end mb-1">
                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'driver_search','value' => 'Motorista / Condutor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'driver_search','value' => 'Motorista / Condutor']); ?>
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
                                <!--[if BLOCK]><![endif]--><?php if($selected_driver_id && !$showNewVisitorForm): ?>
                                    <button type="button"
                                        wire:click="$set('selected_driver_id', null); $set('driver_search', '')"
                                        class="text-xs text-red-500 hover:text-red-700 font-semibold bg-red-50 px-2 py-1 rounded">
                                        Limpar seleção
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            
                            <!--[if BLOCK]><![endif]--><?php if(count($suggestedDrivers) > 0 && !$selected_driver_id && !$showNewVisitorForm): ?>
                                <div class="mb-3">
                                    <p class="text-xs text-gray-500 mb-2">Sugestões baseadas neste veículo:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $suggestedDrivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button"
                                                wire:click="useSuggestedDriver(<?php echo e($driver->id); ?>, '<?php echo e(addslashes($driver->name)); ?>')"
                                                class="px-3 py-1.5 bg-white border border-green-200 text-green-700 rounded-full text-sm hover:bg-green-50 transition-colors shadow-sm flex items-center gap-1">
                                                👤 <?php echo e($driver->name); ?>

                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!--[if BLOCK]><![endif]--><?php if($showNewVisitorForm): ?>
                                <div
                                    class="bg-white p-3 sm:p-4 border border-blue-200 rounded-md mt-2 shadow-sm animate-fade-in-down">
                                    <div class="flex justify-between items-center mb-3 border-b border-blue-100 pb-2">
                                        <h3 class="font-semibold text-sm text-blue-800">Novo Visitante</h3>
                                        <button type="button" wire:click="cancelNewVisitor"
                                            class="text-xs font-bold text-gray-500 hover:text-gray-800 uppercase tracking-wider">Cancelar</button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_visitor_name','value' => 'Nome Completo','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_name','value' => 'Nome Completo','class' => 'text-xs']); ?>
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
                                            <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['wire:model.live' => 'new_visitor_name','id' => 'new_visitor_name','class' => 'block mt-1 w-full text-sm','type' => 'text']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'new_visitor_name','id' => 'new_visitor_name','class' => 'block mt-1 w-full text-sm','type' => 'text']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                            <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'new_visitor_name','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_name','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_visitor_document','value' => 'CPF','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_document','value' => 'CPF','class' => 'text-xs']); ?>
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
                                                <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['wire:model.live' => 'new_visitor_document','id' => 'new_visitor_document','class' => 'block mt-1 w-full text-sm','type' => 'tel','xMask' => '999.999.999-99']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'new_visitor_document','id' => 'new_visitor_document','class' => 'block mt-1 w-full text-sm','type' => 'tel','x-mask' => '999.999.999-99']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'new_visitor_document','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_document','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                            </div>
                                            <div>
                                                <?php if (isset($component)) { $__componentOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3da9d84bb64e4bc2eeebaafabfb2581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'new_visitor_phone','value' => 'Telefone (Opcional)','class' => 'text-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_phone','value' => 'Telefone (Opcional)','class' => 'text-xs']); ?>
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
                                                <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['wire:model' => 'new_visitor_phone','id' => 'new_visitor_phone','class' => 'block mt-1 w-full text-sm','type' => 'tel','xMask' => '(99) 99999-9999']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'new_visitor_phone','id' => 'new_visitor_phone','class' => 'block mt-1 w-full text-sm','type' => 'tel','x-mask' => '(99) 99999-9999']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
                                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'new_visitor_phone','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'new_visitor_phone','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="relative">
                                    <?php if (isset($component)) { $__componentOriginal18c21970322f9e5c938bc954620c12bb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal18c21970322f9e5c938bc954620c12bb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['wire:model.live.debounce.300ms' => 'driver_search','id' => 'driver_search','class' => 'block w-full text-sm sm:text-base '.e($selected_driver_id ? 'bg-green-50 border-green-300 text-green-900 font-semibold' : '').'','placeholder' => 'Buscar motorista existente...','disabled' => $selected_driver_id,'autocomplete' => 'off']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'driver_search','id' => 'driver_search','class' => 'block w-full text-sm sm:text-base '.e($selected_driver_id ? 'bg-green-50 border-green-300 text-green-900 font-semibold' : '').'','placeholder' => 'Buscar motorista existente...','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selected_driver_id),'autocomplete' => 'off']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $attributes = $__attributesOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__attributesOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal18c21970322f9e5c938bc954620c12bb)): ?>
<?php $component = $__componentOriginal18c21970322f9e5c938bc954620c12bb; ?>
<?php unset($__componentOriginal18c21970322f9e5c938bc954620c12bb); ?>
<?php endif; ?>

                                    <!--[if BLOCK]><![endif]--><?php if(!empty($driver_search) && !$selected_driver_id): ?>
                                        <div
                                            class="absolute z-20 w-full bg-white rounded-md shadow-lg mt-1 border border-gray-200">
                                            <!--[if BLOCK]><![endif]--><?php if(count($drivers) > 0): ?>
                                                <ul class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="px-4 py-2 hover:bg-gray-50 cursor-pointer"
                                                            wire:click="selectDriver(<?php echo e($driver->id); ?>, '<?php echo e(addslashes($driver->name)); ?>')">
                                                            <div class="font-medium text-sm text-gray-800">
                                                                <?php echo e($driver->name); ?></div>
                                                            <div class="text-xs text-gray-500">
                                                                CPF:
                                                                <?php echo e(strlen($driver->document) === 11 ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $driver->document) : ($driver->document ?: 'Não informado')); ?>

                                                            </div>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </ul>
                                            <?php else: ?>
                                                <div
                                                    class="p-4 text-center text-sm text-gray-600 bg-gray-50 rounded-md">
                                                    <p class="mb-2">Nenhum motorista encontrado.</p>
                                                    <button type="button" wire:click="prepareNewVisitorForm"
                                                        class="w-full bg-white border border-green-500 text-green-600 py-1.5 rounded text-xs font-bold hover:bg-green-50 transition">
                                                        + Cadastrar "<?php echo e($driver_search); ?>"
                                                    </button>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'selected_driver_id','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'selected_driver_id','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 pt-4 border-t border-gray-100">
                            <div>
                                <label for="entry_reason" class="block text-sm font-medium text-gray-700">Motivo da
                                    Entrada</label>
                                <select id="entry_reason" wire:model.live="entry_reason"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-sm sm:text-base <?php $__errorArgs = ['entry_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">Selecione um motivo</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $predefinedReasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($reason); ?>"><?php echo e($reason); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    <option value="Outro">Outro (Especificar)</option>
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['entry_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($entry_reason === 'Outro'): ?>
                                <div class="animate-fade-in">
                                    <label for="other_reason"
                                        class="block text-sm font-medium text-gray-700">Especifique o Motivo</label>
                                    <input type="text" id="other_reason" wire:model="other_reason"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-sm sm:text-base <?php $__errorArgs = ['other_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['other_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="pt-6 border-t border-gray-100 mt-6">
                            <button type="submit"
                                class="w-full sm:w-auto flex items-center justify-center rounded-md bg-ifnmg-green px-8 py-3 text-sm font-bold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200"
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="save"
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>SALVAR ENTRADA</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            
            
            
            <div x-show="tab === 'saida'" style="display: none;"
                class="bg-white overflow-hidden shadow-sm sm:rounded-b-lg sm:rounded-t-none rounded-lg border border-gray-100">
                <div
                    class="p-4 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Veículos no Pátio</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Localize o veículo e confirme a saída.</p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="exitSearch"
                            placeholder="Buscar no pátio..."
                            class="block w-full rounded-md border-gray-300 pl-9 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-sm py-2">
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                        <table class="min-w-full bg-white divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Placa</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Modelo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Entrada</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $currentVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-red-50/30 transition-colors">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap font-mono font-medium text-sm text-gray-900">
                                            <?php echo e($entry->license_plate); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo e($entry->vehicle_model ?? '-'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="font-medium text-gray-800"><?php echo e($entry->driver->name ?? 'N/A'); ?>

                                            </div>
                                            <div class="text-xs text-gray-500"><?php echo e($entry->driver->telefone ?? ''); ?>

                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo e($entry->entry_at->format('d/m/Y H:i')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button wire:click="confirmExit(<?php echo e($entry->id); ?>)"
                                                class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-bold text-red-600 border border-red-200 shadow-sm hover:bg-red-50 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition">
                                                Registrar Saída
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">O pátio está
                                            vazio ou nenhum veículo encontrado.</td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:hidden">
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $currentVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div
                                class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col justify-between relative overflow-hidden">
                                
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400"></div>

                                <div class="pl-2">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-bold text-lg font-mono text-gray-900 leading-none">
                                                <?php echo e($entry->license_plate); ?></h3>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <?php echo e($entry->vehicle_model ?? 'Modelo não informado'); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="inline-block bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">
                                                <?php echo e($entry->entry_at->format('H:i')); ?>

                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 bg-gray-50 p-2 rounded text-sm text-gray-700">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-gray-400">👤</span>
                                            <span
                                                class="font-medium truncate"><?php echo e($entry->driver->name ?? 'N/A'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pl-2">
                                    <button wire:click="confirmExit(<?php echo e($entry->id); ?>)"
                                        class="w-full flex justify-center items-center gap-2 rounded-md bg-white border border-red-300 px-4 py-2.5 text-sm font-bold text-red-600 shadow-sm hover:bg-red-50 transition-colors active:bg-red-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Registrar Saída
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div
                                class="col-span-1 sm:col-span-2 text-center py-10 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                                <p class="text-gray-500">🚗 Nenhum veículo no pátio.</p>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                </div>
            </div>
        </div>
    <?php else: ?>
        
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">Você tem permissão apenas para visualização. O registro de entradas e saídas
                é restrito aos porteiros e administradores.</span>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($showExitConfirmationModal): ?>
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md"
                @click.away="$wire.set('showExitConfirmationModal', false)">
                <div class="p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">Confirmar Saída de Veículo</h3>
                            <div class="mt-2">
                                <!--[if BLOCK]><![endif]--><?php if($entryToExit): ?>
                                    <p class="text-sm text-gray-600">Tem certeza de que deseja registrar a saída para o
                                        seguinte veículo?</p>
                                    <div class="mt-4 text-sm bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <p><strong>Placa:</strong> <span
                                                class="font-mono text-base"><?php echo e($entryToExit->license_plate); ?></span>
                                        </p>
                                        <p class="mt-1"><strong>Modelo:</strong> <?php echo e($entryToExit->vehicle_model); ?>

                                        </p>
                                        <p class="mt-1"><strong>Condutor:</strong>
                                            <?php echo e($entryToExit->driver->name ?? 'Não informado'); ?></p>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse sm:space-x-2 sm:space-x-reverse gap-2 sm:gap-0 rounded-b-lg">
                    <button wire:click="executeExit"
                        class="w-full sm:w-auto inline-flex justify-center items-center rounded-md bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700">
                        Confirmar Saída
                    </button>
                    <button wire:click="$set('showExitConfirmationModal', false)"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md bg-white px-4 py-2.5 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/livewire/create-private-entry.blade.php ENDPATH**/ ?>