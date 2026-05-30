@php
    $scoreColor = static function (?int $score): string {
        if ($score === null) {
            return 'gray';
        }

        if ($score >= 90) {
            return 'success';
        }

        if ($score >= 50) {
            return 'warning';
        }

        return 'danger';
    };
@endphp

@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Lighthouse report'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Latest Lighthouse report">
            @if (! $this->report)
                <x-filament::empty-state
                    compact
                    description="No Lighthouse report is available for this monitor yet."
                    heading="No Lighthouse data."
                    icon="heroicon-m-bolt"
                />
            @else
                @php($report = $this->report)
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    @foreach ([
                        'Performance' => $report['performance_score'] ?? null,
                        'Accessibility' => $report['accessibility_score'] ?? null,
                        'Best practices' => $report['best_practices_score'] ?? null,
                        'SEO' => $report['seo_score'] ?? null,
                        'PWA' => $report['pwa_score'] ?? null,
                    ] as $label => $score)
                        <x-filament::section compact :heading="$label" secondary>
                            <p class="mt-1 text-2xl font-semibold text-{{ $scoreColor($score) }}-600 dark:text-{{ $scoreColor($score) }}-400">
                                {{ $score ?? '–' }}
                            </p>
                        </x-filament::section>
                    @endforeach
                </div>

                <div class="mt-4 grid gap-4 text-sm md:grid-cols-3">
                    <x-filament::section compact heading="First contentful paint" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">
                            {{ $report['first_contentful_paint_ms'] !== null ? round((float) $report['first_contentful_paint_ms']) . ' ms' : '–' }}
                        </p>
                    </x-filament::section>
                    <x-filament::section compact heading="Largest contentful paint" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">
                            {{ $report['largest_contentful_paint_ms'] !== null ? round((float) $report['largest_contentful_paint_ms']) . ' ms' : '–' }}
                        </p>
                    </x-filament::section>
                    <x-filament::section compact heading="Total blocking time" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">
                            {{ $report['total_blocking_time_ms'] !== null ? round((float) $report['total_blocking_time_ms']) . ' ms' : '–' }}
                        </p>
                    </x-filament::section>
                </div>

                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">Generated at {{ $report['created_at'] ?? 'unknown' }}</p>
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
