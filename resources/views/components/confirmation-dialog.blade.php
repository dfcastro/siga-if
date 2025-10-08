@props(['id' => null])

<div
    x-data="{ show: @entangle($attributes->wire('model')) }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
    style="display: none;"
>
    <div @click.away="show = false" class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">{{ $title }}</h3>
        </div>

        <div class="p-6">
            {{ $content }}
        </div>

        <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-2">
            {{ $footer }}
        </div>
    </div>
</div>