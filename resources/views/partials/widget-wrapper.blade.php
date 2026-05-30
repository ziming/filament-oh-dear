@php
    $heading ??= null;
    $description ??= null;
    $widget ??= $this ?? null;
@endphp

@if ($widget?->loadError)
    <x-filament-widgets::widget>
        @include('filament-oh-dear::partials.callout', [
            'message' => 'Unable to load this widget: ' . $widget->loadError,
        ])
    </x-filament-widgets::widget>
@elseif ($widget && ! $widget->loaded)
    <x-filament-widgets::widget>
        <x-filament::section :heading="$heading" :description="$description" compact>
            <x-filament::empty-state
                compact
                description="Add an Oh Dear API token to load this widget."
                heading="Waiting for configuration."
                icon="heroicon-m-cog"
            />
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
