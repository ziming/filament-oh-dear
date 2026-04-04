<div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-700 shadow-sm dark:border-white/15 dark:bg-gray-900 dark:text-gray-200">
    <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $heading }}</h3>

    <p class="mt-2 leading-6 text-gray-600 dark:text-gray-300">
        {{ $description }}
    </p>

    @if (! empty($lines ?? []))
        <ul class="mt-4 space-y-1 font-mono text-xs text-gray-500 dark:text-gray-400">
            @foreach ($lines as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    @endif
</div>
