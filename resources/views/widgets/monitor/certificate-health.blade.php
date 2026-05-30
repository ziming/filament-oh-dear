@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Certificate health'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Certificate health">
            @if ($this->certificateHealth)
                @php($certificate = $this->certificateHealth)
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <x-filament::section compact heading="Issuer" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['issuer'] ?? 'Unknown' }}</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Valid until" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $certificate['valid_until'] ?? 'Unknown' }}</p>
                    </x-filament::section>
                </div>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $certificate['summary'] }}</p>

                @if (! empty($certificate['checks']))
                    <div class="mt-4 space-y-2 text-sm">
                        @foreach ($certificate['checks'] as $check)
                            <x-filament::section compact secondary>
                                <x-slot name="afterHeader">
                                    @include('filament-oh-dear::partials.status-badge', [
                                        'color' => $check['passed'] ? 'success' : 'danger',
                                        'label' => $check['passed'] ? 'Pass' : 'Fail',
                                    ])
                                </x-slot>

                                <div class="space-y-1">
                                    <p class="font-medium text-gray-950 dark:text-white">{{ $check['label'] ?? $check['type'] }}</p>
                                    @if (! empty($check['message']))
                                        <p class="text-gray-500 dark:text-gray-400">{{ $check['message'] }}</p>
                                    @endif
                                </div>
                            </x-filament::section>
                        @endforeach
                    </div>
                @endif
            @else
                <x-filament::empty-state
                    compact
                    description="This monitor did not return any certificate health details."
                    heading="No certificate health data is available."
                    icon="heroicon-m-shield-check"
                />
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
