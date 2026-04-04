<?php

use Ziming\FilamentOhDear\Exceptions\MonitorOutOfScopeException;
use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('enforces monitor allowlists before loading details', function () {
    $repository = new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    );

    $service = new OhDearDataService($repository);
    $settings = new OhDearSettings(
        apiToken: 'token',
        teamId: 10,
        monitorIds: [2],
        cacheStore: 'array',
        cacheTtl: 300,
        navigationGroup: 'Oh Dear',
        navigationIcon: 'heroicon-o-signal',
        navigationSort: 50,
    );

    expect(fn () => $service->getMonitor($settings, 1))
        ->toThrow(MonitorOutOfScopeException::class);

    expect($repository->monitorCalls)->toBe(0);
});
