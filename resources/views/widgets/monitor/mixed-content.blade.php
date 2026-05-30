@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Mixed content'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Mixed content" description="HTTP assets loaded on an HTTPS page.">
            @if (empty($this->mixedContent))
                <x-filament::empty-state
                    compact
                    description="Oh Dear did not detect any mixed-content issues on this monitor."
                    heading="No mixed content detected."
                    icon="heroicon-m-shield-check"
                    icon-color="success"
                />
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Element</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Insecure URL</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300">Found on</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                            @foreach ($this->mixedContent as $entry)
                                <tr>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $entry['element_name'] }}</td>
                                    <td class="px-4 py-3">
                                        <x-filament::link
                                            color="danger"
                                            href="{{ $entry['mixed_content_url'] }}"
                                            rel="noopener noreferrer"
                                            size="sm"
                                            target="_blank"
                                        >
                                            {{ $entry['mixed_content_url'] }}
                                        </x-filament::link>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-filament::link
                                            color="gray"
                                            href="{{ $entry['found_on_url'] }}"
                                            rel="noopener noreferrer"
                                            size="sm"
                                            target="_blank"
                                        >
                                            {{ $entry['found_on_url'] }}
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
