@php
    $byGroup = $overview['by_group'] ?? [];
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        description="Aggregate health per monitor group."
        heading="Monitors by group"
    >
        @if (empty($byGroup))
            <x-filament::empty-state
                compact
                description="No groups were returned for the configured monitors."
                heading="No groups to display."
                icon="heroicon-m-rectangle-group"
            />
        @else
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Group</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Total</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Healthy</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Issues</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                        @foreach ($byGroup as $name => $counts)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $counts['total'] }}</td>
                                <td class="px-4 py-3 text-success-600 dark:text-success-400">{{ $counts['healthy'] }}</td>
                                <td class="px-4 py-3 text-danger-600 dark:text-danger-400">{{ $counts['issues'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
