@if (! $this->loaded || $this->loadError)
    @include('filament-oh-dear::partials.widget-wrapper', ['heading' => 'Monitor summary'])
@else
    @php($monitor = $this->monitor)
    <x-filament-widgets::widget>
        <x-filament::section heading="Monitor summary">
            <x-slot name="afterHeader">
                @include('filament-oh-dear::partials.status-badge', [
                    'color' => $monitor['result_color'],
                    'label' => $monitor['result_label'],
                ])
            </x-slot>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitor</p>
                    <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $monitor['display_name'] }}</p>
                    <x-filament::link
                        color="gray"
                        href="{{ $monitor['url'] }}"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        {{ $monitor['url'] }}
                    </x-filament::link>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['type_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Group</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['group_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tags</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['tags_label'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last run</p>
                    <p class="mt-1 font-medium text-gray-950 dark:text-white">{{ $monitor['latest_run_display'] }}</p>
                </div>
            </div>
        </x-filament::section>
    </x-filament-widgets::widget>
@endif
