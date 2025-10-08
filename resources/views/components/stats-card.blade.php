@props(['title', 'value', 'color' => 'gray'])

<div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
        <p class="text-3xl font-bold text-{{ $color }}-600">{{ $value }}</p>
    </div>
    <div class="bg-{{ $color }}-100 p-3 rounded-full">
        {{ $slot }} </div>
</div>