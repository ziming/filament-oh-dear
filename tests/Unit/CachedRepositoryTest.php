<?php

use Ziming\FilamentOhDear\Repositories\CachedOhDearDataRepository;
use Ziming\FilamentOhDear\Repositories\SdkOhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearDataNormalizer;
use Ziming\FilamentOhDear\Support\OhDearSettings;

it('generates stable cache keys from settings and arguments', function () {
    $repository = new CachedOhDearDataRepository(
        new SdkOhDearDataRepository(new OhDearDataNormalizer),
        app('cache'),
    );

    $settings = new OhDearSettings(
        apiToken: 'token',
        teamId: 10,
        monitorIds: [1, 2],
        cacheStore: 'array',
        cacheTtl: 300,
        navigationGroup: 'Oh Dear',
        navigationIcon: 'heroicon-o-signal',
        navigationSort: 50,
    );

    $first = $repository->makeCacheKey('monitor', $settings, [1]);
    $second = $repository->makeCacheKey('monitor', $settings, [1]);
    $third = $repository->makeCacheKey('monitor', $settings, [2]);

    expect($first)->toBe($second)
        ->and($first)->not->toBe($third);
});
