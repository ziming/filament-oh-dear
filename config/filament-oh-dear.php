<?php

return [
    'api_token' => env('OH_DEAR_API_TOKEN'),
    'team_id' => env('OH_DEAR_TEAM_ID') !== null ? (int) env('OH_DEAR_TEAM_ID') : null,
    'monitor_ids' => array_values(array_filter(array_map(
        static fn (string $id): ?int => is_numeric($id = trim($id)) ? (int) $id : null,
        explode(',', (string) env('OH_DEAR_MONITOR_IDS', '')),
    ))),
    'cache' => [
        'store' => env('OH_DEAR_CACHE_STORE'),
        'ttl' => (int) env('OH_DEAR_CACHE_TTL', 300),
    ],
    'navigation' => [
        'group' => 'Oh Dear',
        'icon' => 'heroicon-o-signal',
        'sort' => 50,
    ],
];
