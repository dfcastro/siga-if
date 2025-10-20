@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm']) }}>
