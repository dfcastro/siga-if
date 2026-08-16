<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['model', 'label', 'placeholder', 'results', 'selectedText']));

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

foreach (array_filter((['model', 'label', 'placeholder', 'results', 'selectedText']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{ open: true }" @click.away="open = false" class="relative">
    <label for="<?php echo e($model); ?>" class="block text-sm font-medium text-gray-700"><?php echo e($label); ?></label>

    <!--[if BLOCK]><![endif]--><?php if($selectedText): ?>
        <div class="mt-1 flex items-center justify-between p-2 bg-gray-100 border border-gray-300 rounded-md">
            <span class="text-gray-800"><?php echo e($selectedText); ?></span>
            <button type="button" wire:click="clearSelection('<?php echo e($model); ?>')"
                class="text-red-500 hover:text-red-700 font-bold text-lg">
                &times;
            </button>
        </div>
    <?php else: ?>
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            
            <input type="text" id="<?php echo e($model); ?>" wire:model.defer="<?php echo e($model); ?>"
                x-on:input.debounce.300ms="$wire.call('runSearch', '<?php echo e($model); ?>', $event.target.value)"
                @focus="open = true" placeholder="<?php echo e($placeholder); ?>" autocomplete="off"
                class="mt-1 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green <?php $__errorArgs = [Str::replace('_search', '_id', $model)];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        </div>

        <div x-show="open && <?php echo \Illuminate\Support\Js::from(is_array($results) && count($results) > 0)->toHtml() ?>" x-transition
            class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
            <ul>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li wire:click="selectResult('<?php echo e($model); ?>', <?php echo e($result['id']); ?>, '<?php echo e(addslashes($result['name'] ?? $result['model'] . ' (' . $result['license_plate'] . ')')); ?>')"
                        class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm">
                        <?php echo e($result['name'] ?? $result['model'] . ' (' . $result['license_plate'] . ')'); ?>

                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </ul>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php $__errorArgs = [Str::replace('_search', '_id', $model)];
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
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/components/searchable-select.blade.php ENDPATH**/ ?>