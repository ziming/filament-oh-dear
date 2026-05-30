@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Broken links'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Broken links">
            @if (empty($this->brokenLinks))
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
                            @foreach ($this->brokenLinks as $link)
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
    </x-filament-widgets::widget>
@endif
