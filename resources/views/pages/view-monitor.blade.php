<x-filament-panels::page>
    @if ($this->requiresSetup)
        @include('filament-oh-dear::partials.state', [
            'heading' => 'Finish the Oh Dear setup',
            'description' => 'Add an API token before opening monitor details.',
            'lines' => [
                'OH_DEAR_API_TOKEN=',
                'OH_DEAR_TEAM_ID=',
                'OH_DEAR_MONITOR_IDS=1,2,3',
                'php artisan filament-oh-dear:verify',
            ],
        ])
    @elseif ($this->loadError)
        @include('filament-oh-dear::partials.callout', [
            'message' => 'Unable to load this monitor right now: ' . $this->loadError,
        ])
    @endif
</x-filament-panels::page>
