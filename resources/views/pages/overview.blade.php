<x-filament-panels::page>
    @if ($this->requiresSetup)
        @include('filament-oh-dear::partials.state', [
            'heading' => 'Finish the Oh Dear setup',
            'description' => 'Add an API token first. The panel will stay read-only and stateless, and you can verify the connection from the CLI.',
            'lines' => [
                'OH_DEAR_API_TOKEN=',
                'OH_DEAR_TEAM_ID=',
                'OH_DEAR_MONITOR_IDS=1,2,3',
                'php artisan filament-oh-dear:verify',
            ],
        ])
    @elseif ($this->loadError)
        @include('filament-oh-dear::partials.callout', [
            'message' => 'Unable to load the overview right now: ' . $this->loadError,
        ])
    @endif
</x-filament-panels::page>
