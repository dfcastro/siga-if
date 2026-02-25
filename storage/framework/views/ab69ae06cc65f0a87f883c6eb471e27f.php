<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-8"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> 

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <a href="<?php echo e(route('entries.create')); ?>"
                    class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-blue-500 transition-all hover:bg-blue-50 group">
                    <div class="bg-blue-100 p-4 rounded-full group-hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 group-hover:text-blue-700">Registrar Entrada/Saída</h4>
                        <p class="text-sm text-gray-500 group-hover:text-blue-600">Controle de veículos particulares</p>
                    </div>
                    
                    <div class="ml-auto text-gray-300 group-hover:text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>

                
                <a href="<?php echo e(route('fleet.index')); ?>"
                    class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-green-500 transition-all hover:bg-green-50 group">
                    <div class="bg-green-100 p-4 rounded-full group-hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 group-hover:text-green-700">Diário de Bordo</h4>
                        <p class="text-sm text-gray-500 group-hover:text-green-600">Gestão da frota oficial</p>
                    </div>
                    
                    <div class="ml-auto text-gray-300 group-hover:text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 sm:p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Olá, <?php echo e(Auth::user()->name); ?>!</h3>
                        <p class="text-sm text-gray-500">Bem-vindo ao painel de controle do SIGA-IF.</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded">
                        <?php echo e(now()->format('d/m/Y')); ?>

                    </span>
                </div>
            </div>

            
            <div class="flex flex-col">
                
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('pending-exits');

$__html = app('livewire')->mount($__name, $__params, 'lw-3417417638-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            
            <div>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard-stats');

$__html = app('livewire')->mount($__name, $__params, 'lw-3417417638-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/dashboard.blade.php ENDPATH**/ ?>