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
    @elseif ($this->detail)
        @php
            $monitor = $this->detail['monitor'];
            $metrics = collect($this->detail['metrics']);
            $metricValues = $metrics->pluck('latency_ms')->filter()->values();
            $chartPoints = $metricValues->isNotEmpty()
                ? $metricValues->map(function ($value, $index) use ($metricValues) {
                    $count = max($metricValues->count() - 1, 1);
                    $max = max($metricValues->max(), 1);
                    $min = min($metricValues->min(), $max);
                    $range = max($max - $min, 1);
                    $x = ($index / $count) * 100;
                    $y = 100 - ((($value - $min) / $range) * 100);

                    return round($x, 2) . ',' . round($y, 2);
                })->implode(' ')
                : null;
        @endphp

        @if (! empty($this->detail['warnings']))
            <div class="space-y-3">
                @foreach ($this->detail['warnings'] as $warning)
                    @include('filament-oh-dear::partials.callout', ['message' => $warning])
                @endforeach
            </div>
        @endif

        <x-filament::section
            class="mt-6"
            heading="Monitor summary"
        >
            <x-slot name="afterHeader">
                @include('filament-oh-dear::partials.status-badge', [
                    'color' => $monitor['result_color'],
                    'label' => $monitor['result_label'],
                ])
            </x-slot>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor</p>
                    <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $monitor['display_name'] }}</p>
                    <x-filament::link
                        color="gray"
                        href="{{ $monitor['url'] }}"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        {{ $monitor['url'] }}
                    </x-filament::link>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['type_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Group</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['group_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tags</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['tags_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last run</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['latest_run_display'] }}</p>
                </div>
            </div>
        </x-filament::section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <x-filament::section heading="Current check summaries">
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->detail['check_summaries'] as $summary)
                        <x-filament::section
                            compact
                            :heading="$summary['label']"
                            secondary
                        >
                            <x-slot name="afterHeader">
                                @include('filament-oh-dear::partials.status-badge', [
                                    'color' => $summary['color'],
                                    'label' => $summary['result'] ?? 'unknown',
                                ])
                            </x-slot>

                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $summary['display_summary'] }}</p>
                        </x-filament::section>
                    @endforeach
                </div>
            </x-filament::section>

            <x-filament::section heading="Latency (24h)">
                @if ($chartPoints)
                    <x-filament::section
                        compact
                        secondary
                    >
                        <svg viewBox="0 0 100 100" class="h-48 w-full" preserveAspectRatio="none" aria-hidden="true">
                            <polyline
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                points="{{ $chartPoints }}"
                                class="text-primary-500"
                            />
                        </svg>
                    </x-filament::section>

                    <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
                        <x-filament::section compact heading="Avg" secondary>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->avg(), 2) }} ms</p>
                        </x-filament::section>
                        <x-filament::section compact heading="Min" secondary>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->min(), 2) }} ms</p>
                        </x-filament::section>
                        <x-filament::section compact heading="Max" secondary>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->max(), 2) }} ms</p>
                        </x-filament::section>
                    </div>
                @else
                    <x-filament::empty-state
                        compact
                        description="No latency samples were returned for this monitor type."
                        heading="No performance metrics are available yet."
                        icon="heroicon-m-chart-bar"
                    />
                @endif
            </x-filament::section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <x-filament::section heading="Certificate health">
                @if ($this->detail['certificate_health'])
                    @php($certificate = $this->detail['certificate_health'])
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <x-filament::section compact heading="Issuer" secondary>
                            <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['issuer'] ?? 'Unknown' }}</p>
                        </x-filament::section>
                        <x-filament::section compact heading="Valid until" secondary>
                            <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['valid_until'] ?? 'Unknown' }}</p>
                        </x-filament::section>
                    </div>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $certificate['summary'] }}</p>

                    @if (! empty($certificate['checks']))
                        <div class="mt-4 space-y-2 text-sm">
                            @foreach ($certificate['checks'] as $check)
                                <x-filament::section compact secondary>
                                    <x-slot name="afterHeader">
                                        @include('filament-oh-dear::partials.status-badge', [
                                            'color' => $check['passed'] ? 'success' : 'danger',
                                            'label' => $check['passed'] ? 'Pass' : 'Fail',
                                        ])
                                    </x-slot>

                                    <div class="space-y-1">
                                        <p class="font-medium text-gray-950 dark:text-white">{{ $check['label'] }}</p>
                                        @if ($check['message'])
                                            <p class="text-gray-500 dark:text-gray-400">{{ $check['message'] }}</p>
                                        @endif
                                    </div>
                                </x-filament::section>
                            @endforeach
                        </div>
                    @endif
                @else
                    <x-filament::empty-state
                        compact
                        description="This monitor did not return any certificate health details."
                        heading="No certificate health data is available."
                        icon="heroicon-m-shield-check"
                    />
                @endif
            </x-filament::section>

            <x-filament::section heading="Broken links">
                @if (empty($this->detail['broken_links']))
                    <x-filament::empty-state
                        compact
                        description="The Oh Dear API did not return any broken link records for this monitor."
                        heading="No broken links were returned."
                        icon="heroicon-m-link"
                    />
                @else
                    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Broken URL</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Found on</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                                @foreach ($this->detail['broken_links'] as $link)
                                    <tr>
                                        <td class="px-4 py-3">
                                            @include('filament-oh-dear::partials.status-badge', [
                                                'color' => filled($link['status_code'] ?? null) ? 'danger' : 'gray',
                                                'label' => $link['status_code'] ?? 'n/a',
                                            ])
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-950 dark:text-white">{{ $link['link_text'] }}</p>
                                            <x-filament::link
                                                color="gray"
                                                href="{{ $link['crawled_url'] }}"
                                                rel="noopener noreferrer"
                                                size="sm"
                                                target="_blank"
                                            >
                                                {{ $link['crawled_url'] }}
                                            </x-filament::link>
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-filament::link
                                                color="gray"
                                                href="{{ $link['found_on_url'] }}"
                                                rel="noopener noreferrer"
                                                size="sm"
                                                target="_blank"
                                            >
                                                {{ $link['found_on_url'] }}
                                            </x-filament::link>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <x-filament::section
            class="mt-6"
            heading="Recent downtime (30d)"
        >
            @if (empty($this->detail['downtime_periods']))
                <x-filament::empty-state
                    compact
                    description="No downtime periods were returned for the last 30 days."
                    heading="No recent downtime was reported."
                    icon="heroicon-m-clock"
                />
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Started</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Ended</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                            @foreach ($this->detail['downtime_periods'] as $downtime)
                                <tr>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $downtime['started_at_display'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $downtime['ended_at_display'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $downtime['notes'] ?? 'No notes' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament-panels::page>
