@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Current check summaries'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Current check summaries">
            @if (empty($this->checkSummaries))
                <x-filament::empty-state
                    compact
                    description="No check summaries were returned for this monitor."
                    heading="No checks reported yet."
                    icon="heroicon-m-clipboard-document-check"
                />
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->checkSummaries as $summary)
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
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
