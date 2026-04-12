<?php

use Ziming\FilamentOhDear\Repositories\CachedOhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('rehydrates a monitor from cached arrays instead of cached objects', function () {
    $inner = new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    );

    $repository = new CachedOhDearDataRepository($inner, app('cache'));

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

    $first = $repository->monitor($settings, 1);
    $second = $repository->monitor($settings, 1);

    expect($first->id)->toBe(1)
        ->and($second->id)->toBe(1)
        ->and($second->checks['uptime']->summary)->toBe('Everything looks good.')
        ->and($inner->monitorCalls)->toBe(1);
});
