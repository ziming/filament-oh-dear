@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Maintenance periods'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Maintenance periods" description="Scheduled or recent maintenance windows.">
            @if (empty($this->periods))
                <x-filament::empty-state
                    compact
                    description="There are no maintenance periods configured for this monitor."
                    heading="No maintenance windows."
                    icon="heroicon-m-wrench-screwdriver"
                />
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Name</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Starts</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Ends</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                            @foreach ($this->periods as $period)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $period['name'] ?? 'Unnamed window' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $period['starts_at'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $period['ends_at'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
