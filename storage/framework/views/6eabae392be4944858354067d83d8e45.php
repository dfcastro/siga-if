<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'value', 'color' => 'gray']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'value', 'color' => 'gray']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-gray-500"><?php echo e($title); ?></p>
        <p class="text-3xl font-bold text-<?php echo e($color); ?>-600"><?php echo e($value); ?></p>
    </div>
    <div class="bg-<?php echo e($color); ?>-100 p-3 rounded-full">
        <?php echo e($slot); ?> </div>
</div><?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/components/stats-card.blade.php ENDPATH**/ ?>