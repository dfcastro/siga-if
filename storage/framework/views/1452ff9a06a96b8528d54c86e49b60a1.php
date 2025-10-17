<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['for' => null, 'messages' => null]));

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

foreach (array_filter((['for' => null, 'messages' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!--[if BLOCK]><![endif]--><?php if($for && $errors->has($for)): ?>
    <p <?php echo e($attributes->merge(['class' => 'text-sm text-red-600'])); ?>>
        <?php echo e($errors->first($for)); ?>

    </p>
<?php elseif($messages): ?>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = (array) $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p <?php echo e($attributes->merge(['class' => 'text-sm text-red-600'])); ?>>
            <?php echo e($message); ?>

        </p>
        <?php break; ?> 
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
<?php endif; ?><!--[if ENDBLOCK]><![endif]--><?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/components/input-error.blade.php ENDPATH**/ ?>