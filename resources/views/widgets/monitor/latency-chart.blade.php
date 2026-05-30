@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Latency (24h)'])
@else
    @php
        $metrics = collect($this->metrics);
        $values = $metrics->pluck('latency_ms')->filter()->values();
        $chartPoints = null;

        if ($values->isNotEmpty()) {
            $count = max($values->count() - 1, 1);
            $max = max($values->max(), 1);
            $min = min($values->min(), $max);
            $range = max($max - $min, 1);

            $chartPoints = $values->map(function ($value, $index) use ($count, $min, $range) {
                $x = ($index / $count) * 100;
                $y = 100 - ((($value - $min) / $range) * 100);

                return round($x, 2) . ',' . round($y, 2);
            })->implode(' ');
        }
    @endphp

    <x-filament-widgets::widget>
        <x-filament::section heading="Latency (24h)">
            @if ($chartPoints)
                <x-filament::section
                    compact
                    secondary
                >
                    <svg viewBox="0 0 100 100" class="h-48 w-full" preserveAspectRatio="none" aria-hidden="true">
                        <polyline
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            points="{{ $chartPoints }}"
                            class="text-primary-500"
                        />
                    </svg>
                </x-filament::section>

                <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
                    <x-filament::section compact heading="Avg" secondary>
                        <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($values->avg(), 2) }} ms</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Min" secondary>
                        <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($values->min(), 2) }} ms</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Max" secondary>
                        <p class="mt-1 font-semibold text-gray-950 dark:text-white">{{ round($values->max(), 2) }} ms</p>
                    </x-filament::section>
                </div>
            @else
                <x-filament::empty-state
                    compact
                    description="No latency samples were returned for this monitor type."
                    heading="No performance metrics are available yet."
                    icon="heroicon-m-chart-bar"
                />
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
