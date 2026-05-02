@props([
    'color' => 'gray',
    'label',
])

<x-filament::badge :color="$color">
    {{ $label }}
</x-filament::badge>
