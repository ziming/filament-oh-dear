<x-filament::empty-state
    :description="$description"
    :heading="$heading"
    icon="heroicon-m-wrench-screwdriver"
>
    <x-slot name="footer">
        @if (! empty($lines ?? []))
            <ul class="space-y-1 text-left font-mono text-xs">
                @foreach ($lines as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        @endif
    </x-slot>
</x-filament::empty-state>
