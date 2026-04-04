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
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total monitors</p>
                <p class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $this->overview['total_monitors'] ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-500/10">
                <p class="text-sm text-emerald-700 dark:text-emerald-200">Healthy monitors</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-900 dark:text-emerald-50">{{ $this->overview['healthy_monitors'] ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10">
                <p class="text-sm text-amber-700 dark:text-amber-200">Active issues</p>
                <p class="mt-2 text-3xl font-semibold text-amber-900 dark:text-amber-50">{{ $this->overview['monitors_with_active_issues'] ?? 0 }}</p>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 shadow-sm dark:border-rose-500/30 dark:bg-rose-500/10">
                <p class="text-sm text-rose-700 dark:text-rose-200">Certificate issues</p>
                <p class="mt-2 text-3xl font-semibold text-rose-900 dark:text-rose-50">{{ $this->overview['certificate_issues'] ?? 0 }}</p>
            </div>
        </div>

        <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Needs attention</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitors with warnings, failures, or certificate issues.</p>
                </div>

                <a
                    class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400"
                    href="{{ \Ziming\FilamentOhDear\Pages\MonitorsPage::getUrl() }}"
                >
                    View all monitors
                </a>
            </div>

            @if (empty($this->overview['needs_attention']))
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">All scoped monitors currently look healthy.</p>
            @else
                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
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
                                        <a class="font-medium text-gray-950 hover:text-primary-600 dark:text-white dark:hover:text-primary-400" href="{{ \Ziming\FilamentOhDear\Pages\ViewMonitorPage::getUrl(['monitor' => $monitor['id']]) }}">
                                            {{ $monitor['display_name'] }}
                                        </a>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $monitor['url'] }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                            @class([
                                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-100' => $monitor['result_color'] === 'success',
                                                'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-100' => $monitor['result_color'] === 'warning',
                                                'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-100' => $monitor['result_color'] === 'danger',
                                                'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200' => $monitor['result_color'] === 'gray',
                                            ])
                                        ">
                                            {{ $monitor['result_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $monitor['issue_summary'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
</x-filament-panels::page>
