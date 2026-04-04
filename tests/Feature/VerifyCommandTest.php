<?php

use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('verifies the configured token and scope successfully', function () {
    config()->set('filament-oh-dear.api_token', 'test-token');
    config()->set('filament-oh-dear.team_id', 10);
    config()->set('filament-oh-dear.monitor_ids', [1]);

    app()->instance(OhDearDataRepository::class, new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    ));

    $this->artisan('filament-oh-dear:verify')
        ->expectsOutputToContain('Authenticated as Jane Doe')
        ->expectsOutputToContain('Team scope OK for team [10]')
        ->expectsOutputToContain('Monitor [1] OK: Primary Site')
        ->assertSuccessful();
});

it('fails verification with actionable output when the token is missing', function () {
    config()->set('filament-oh-dear.api_token', null);

    $this->artisan('filament-oh-dear:verify')
        ->expectsOutputToContain('Missing configuration')
        ->expectsOutputToContain('OH_DEAR_API_TOKEN')
        ->assertFailed();
});
