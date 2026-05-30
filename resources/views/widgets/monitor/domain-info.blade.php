@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Domain info'])
@else
    <x-filament-widgets::widget>
        <x-filament::section heading="Domain info" description="WHOIS / RDAP snapshot for the registered domain.">
            @if (! $this->domain)
                <x-filament::empty-state
                    compact
                    description="Oh Dear does not have a domain record for this monitor."
                    heading="No domain information."
                    icon="heroicon-m-globe-alt"
                />
            @else
                @php($domain = $this->domain)
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <x-filament::section compact heading="Expires" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $domain['expires_at'] ?? '—' }}</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Registered" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $domain['registered_at'] ?? '—' }}</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Last changed" secondary>
                        <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $domain['last_changed_at'] ?? '—' }}</p>
                    </x-filament::section>
                    <x-filament::section compact heading="Statuses" secondary>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ ! empty($domain['domain_statuses']) ? implode(', ', $domain['domain_statuses']) : 'None reported' }}
                        </p>
                    </x-filament::section>
                </div>
            @endif
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
