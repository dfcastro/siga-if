@props(['active', 'icon' => null])

@php
    $baseClasses =
        'inline-flex items-center gap-x-2 px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition duration-150 ease-in-out';

    $activeClasses = 'bg-ifnmg-green-100 text-ifnmg-green-800';

    $inactiveClasses = 'text-gray-500 hover:bg-gray-100 hover:text-gray-700';

    $classes = $active ? $baseClasses . ' ' . $activeClasses : $baseClasses . ' ' . $inactiveClasses;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if ($icon)
        <x-icon :name="$icon" />
    @endif
    <span>{{ $slot }}</span>
</a>
