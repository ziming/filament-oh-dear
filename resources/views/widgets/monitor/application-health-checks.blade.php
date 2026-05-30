@php
    $statusColor = static fn (?string $status): string => match ($status) {
        'succeeded' => 'success',
        'warning' => 'warning',
        'failed' => 'danger',
        default => 'gray',
    };
@endphp

@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Application health checks'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Application health checks" description="Custom health checks reported by the application.">
            @if (empty($this->checks))
                <x-filament::empty-state
                    compact
                    description="No application health checks were reported for this monitor."
                    heading="No application health checks."
                    icon="heroicon-m-heart"
                />
            @else
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($this->checks as $check)
                        <x-filament::section compact secondary :heading="$check['label'] ?? $check['name']">
                            <x-slot name="afterHeader">
                                @include('filament-oh-dear::partials.status-badge', [
                                    'color' => $statusColor($check['status'] ?? null),
                                    'label' => $check['status'] ?? 'unknown',
                                ])
                            </x-slot>

                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ $check['short_summary'] ?? $check['message'] ?? 'No summary provided.' }}
                            </p>

                            @if (! empty($check['snoozed']))
                                <p class="mt-2 text-xs uppercase tracking-wide text-warning-600 dark:text-warning-400">Snoozed</p>
                            @endif
                        </x-filament::section>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
