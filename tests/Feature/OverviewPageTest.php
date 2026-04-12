<?php

use Ziming\FilamentOhDear\Pages\OverviewPage;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('shows a setup empty state when the api token is missing', function () {
    config()->set('filament-oh-dear.api_token', null);

    $this->get(OverviewPage::getUrl())
        ->assertOk()
        ->assertSee('Finish the Oh Dear setup')
        ->assertSee('php artisan filament-oh-dear:verify');
});

it('renders overview stats with the native filament widget', function () {
    config()->set('filament-oh-dear.api_token', 'test-token');

    app()->instance(OhDearDataRepository::class, new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    ));

    $this->get(OverviewPage::getUrl())
        ->assertOk()
        ->assertSee('Total monitors')
        ->assertSee('Healthy monitors')
        ->assertSee('Active issues')
        ->assertSee('Certificate issues')
        ->assertSee('Needs attention')
        ->assertSee('API Ping')
        ->assertDontSee('Primary Site');
});
