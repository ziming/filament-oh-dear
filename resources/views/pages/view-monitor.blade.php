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

        <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor</p>
                    <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $monitor['display_name'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $monitor['url'] }}</p>
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
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overall result</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['result_label'] }}</p>
                </div>
            </div>
        </section>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Current check summaries</h2>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @foreach ($this->detail['check_summaries'] as $summary)
                        <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="font-medium text-gray-950 dark:text-white">{{ $summary['label'] }}</h3>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                    @class([
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-100' => $summary['color'] === 'success',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-100' => $summary['color'] === 'warning',
                                        'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-100' => $summary['color'] === 'danger',
                                        'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200' => $summary['color'] === 'gray',
                                    ])
                                ">
                                    {{ $summary['result'] ?? 'unknown' }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $summary['display_summary'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Latency (24h)</h2>

                @if ($chartPoints)
                    <div class="mt-4 rounded-xl border border-gray-200 p-4 dark:border-white/10">
                        <svg viewBox="0 0 100 100" class="h-48 w-full" preserveAspectRatio="none" aria-hidden="true">
                            <polyline
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                points="{{ $chartPoints }}"
                                class="text-primary-500"
                            />
                        </svg>
                    </div>

                    <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                            <p class="text-gray-500 dark:text-gray-400">Avg</p>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->avg(), 2) }} ms</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                            <p class="text-gray-500 dark:text-gray-400">Min</p>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->min(), 2) }} ms</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                            <p class="text-gray-500 dark:text-gray-400">Max</p>
                            <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($metricValues->max(), 2) }} ms</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No performance metrics are available for this monitor type yet.</p>
                @endif
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Certificate health</h2>

                @if ($this->detail['certificate_health'])
                    @php($certificate = $this->detail['certificate_health'])
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Issuer</p>
                            <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['issuer'] ?? 'Unknown' }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-4 dark:bg-white/5">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Valid until</p>
                            <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['valid_until'] ?? 'Unknown' }}</p>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $certificate['summary'] }}</p>

                    @if (! empty($certificate['checks']))
                        <div class="mt-4 space-y-2 text-sm">
                            @foreach ($certificate['checks'] as $check)
                                <div class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 px-4 py-3 dark:border-white/10">
                                    <div>
                                        <p class="font-medium text-gray-950 dark:text-white">{{ $check['label'] }}</p>
                                        @if ($check['message'])
                                            <p class="text-gray-500 dark:text-gray-400">{{ $check['message'] }}</p>
                                        @endif
                                    </div>
                                    <span class="font-medium {{ $check['passed'] ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">
                                        {{ $check['passed'] ? 'Pass' : 'Fail' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No certificate health data is available for this monitor.</p>
                @endif
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Broken links</h2>

                @if (empty($this->detail['broken_links']))
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No broken links were returned for this monitor.</p>
                @else
                    <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
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
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $link['status_code'] ?? 'n/a' }}</td>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-950 dark:text-white">{{ $link['link_text'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $link['crawled_url'] }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $link['found_on_url'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Recent downtime (30d)</h2>

            @if (empty($this->detail['downtime_periods']))
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No downtime was returned for the last 30 days.</p>
            @else
                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
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
        </section>
    @endif
</x-filament-panels::page>
