@props(['model', 'label', 'placeholder', 'results', 'selectedText'])

<div x-data="{ open: true }" @click.away="open = false" class="relative">
    <label for="{{ $model }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>

    @if ($selectedText)
        <div class="mt-1 flex items-center justify-between p-2 bg-gray-100 border border-gray-300 rounded-md">
            <span class="text-gray-800">{{ $selectedText }}</span>
            <button type="button" wire:click="clearSelection('{{ $model }}')"
                class="text-red-500 hover:text-red-700 font-bold text-lg">
                &times;
            </button>
        </div>
    @else
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            {{-- 
                CORREÇÃO DEFINITIVA:
                - Usamos $wire.call() para invocar o método PHP diretamente.
                - Esta é a forma mais robusta de comunicação a partir de um modal.
            --}}
            <input type="text" id="{{ $model }}" wire:model.defer="{{ $model }}"
                x-on:input.debounce.300ms="$wire.call('runSearch', '{{ $model }}', $event.target.value)"
                @focus="open = true" placeholder="{{ $placeholder }}" autocomplete="off"
                class="mt-1 block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green @error(Str::replace('_search', '_id', $model)) border-red-500 @enderror">
        </div>

        <div x-show="open && @js(is_array($results) && count($results) > 0)" x-transition
            class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
            <ul>
                @foreach ($results as $result)
                    <li wire:click="selectResult('{{ $model }}', {{ $result['id'] }}, '{{ addslashes($result['name'] ?? $result['model'] . ' (' . $result['license_plate'] . ')') }}')"
                        class="px-4 py-3 cursor-pointer hover:bg-gray-100 text-sm">
                        {{ $result['name'] ?? $result['model'] . ' (' . $result['license_plate'] . ')' }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @error(Str::replace('_search', '_id', $model))
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
