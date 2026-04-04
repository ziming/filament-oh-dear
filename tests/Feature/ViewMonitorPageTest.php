<?php

use Ziming\FilamentOhDear\Pages\ViewMonitorPage;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('renders summaries metrics broken links and downtime on the detail page', function () {
    config()->set('filament-oh-dear.api_token', 'test-token');

    app()->instance(OhDearDataRepository::class, new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
        checkSummaries: OhDearFixtures::detailSummaries(),
        metrics: OhDearFixtures::metrics(),
        certificateHealth: OhDearFixtures::certificateHealth(),
        brokenLinks: OhDearFixtures::brokenLinks(),
        downtime: OhDearFixtures::downtime(),
    ));

    $this->get(ViewMonitorPage::getUrl(['monitor' => 1]))
        ->assertOk()
        ->assertSee('Primary Site')
        ->assertSee('Current check summaries')
        ->assertSee('Latency (24h)')
        ->assertSee('Broken links')
        ->assertSee('Recent downtime (30d)');
});

it('returns a 404 when a monitor is outside the configured scope', function () {
    config()->set('filament-oh-dear.api_token', 'test-token');
    config()->set('filament-oh-dear.monitor_ids', [2]);

    app()->instance(OhDearDataRepository::class, new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    ));

    $this->get(ViewMonitorPage::getUrl(['monitor' => 1]))
        ->assertNotFound();
});
