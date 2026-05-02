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
    @else
        <x-filament::section
            description="Monitors with warnings, failures, or certificate issues."
            heading="Needs attention"
        >
            <x-slot name="afterHeader">
                <x-filament::link
                    color="primary"
                    href="{{ \Ziming\FilamentOhDear\Pages\MonitorsPage::getUrl() }}"
                >
                    View all monitors
                </x-filament::link>
            </x-slot>

            @if (empty($this->overview['needs_attention']))
                <x-filament::empty-state
                    compact
                    description="No scoped monitors currently need attention."
                    heading="All scoped monitors currently look healthy."
                    icon="heroicon-m-check-circle"
                    icon-color="success"
                />
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Monitor</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Result</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Issue summary</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                            @foreach ($this->overview['needs_attention'] as $monitor)
                                <tr>
                                    <td class="px-4 py-3">
                                        <x-filament::link
                                            color="gray"
                                            href="{{ \Ziming\FilamentOhDear\Pages\ViewMonitorPage::getUrl(['monitor' => $monitor['id']]) }}"
                                            weight="medium"
                                        >
                                            {{ $monitor['display_name'] }}
                                        </x-filament::link>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $monitor['url'] }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @include('filament-oh-dear::partials.status-badge', [
                                            'color' => $monitor['result_color'],
                                            'label' => $monitor['result_label'],
                                        ])
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $monitor['issue_summary'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>
