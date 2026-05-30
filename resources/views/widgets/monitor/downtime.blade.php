@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Recent downtime'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Recent downtime ({{ $this->days }}d)">
            @if (empty($this->downtimePeriods))
                <x-filament::empty-state
                    compact
                    description="No downtime periods were returned for the selected window."
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
                            @foreach ($this->downtimePeriods as $downtime)
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
    </x-filament-widgets::widget>
@endif
