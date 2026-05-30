@php
    use Ziming\FilamentOhDear\Pages\MonitorsPage;
    use Ziming\FilamentOhDear\Pages\ViewMonitorPage;

    $needsAttention = $overview['needs_attention'] ?? [];
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        description="Monitors with warnings, failures, or certificate issues."
        heading="Needs attention"
    >
        <x-slot name="afterHeader">
            <x-filament::link
                color="primary"
                href="{{ MonitorsPage::getUrl() }}"
            >
                View all monitors
            </x-filament::link>
        </x-slot>

        @if (empty($needsAttention))
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
                        @foreach ($needsAttention as $monitor)
                            <tr>
                                <td class="px-4 py-3">
                                    <x-filament::link
                                        color="gray"
                                        href="{{ ViewMonitorPage::getUrl(['monitor' => $monitor['id']]) }}"
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
</x-filament-widgets::widget>
