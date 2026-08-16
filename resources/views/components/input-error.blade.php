@props(['for' => null, 'messages' => null])

@if ($for && $errors->has($for))
    <p {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>
        {{ $errors->first($for) }}
    </p>
@elseif ($messages)
    @foreach ((array) $messages as $message)
        <p {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>
            {{ $message }}
        </p>
        @break {{-- Geralmente, exibimos apenas a primeira mensagem de erro --}}
    @endforeach
@endif