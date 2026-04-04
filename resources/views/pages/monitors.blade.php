<x-filament-panels::page>
    @if ($this->requiresSetup)
        @include('filament-oh-dear::partials.state', [
            'heading' => 'Finish the Oh Dear setup',
            'description' => 'Add an API token so the monitor table can query the Oh Dear API at runtime.',
            'lines' => [
                'OH_DEAR_API_TOKEN=',
                'OH_DEAR_TEAM_ID=',
                'OH_DEAR_MONITOR_IDS=1,2,3',
                'php artisan filament-oh-dear:verify',
            ],
        ])
    @else
        @if ($this->loadError)
            <div class="mb-4">
                @include('filament-oh-dear::partials.callout', [
                    'message' => 'Unable to refresh the monitor table: ' . $this->loadError,
                ])
            </div>
        @endif

        {{ $this->table }}
    @endif
</x-filament-panels::page>
