<?php

use Livewire\Livewire;
use Ziming\FilamentOhDear\Pages\MonitorsPage;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fakes\FakeOhDearDataRepository;
use Ziming\FilamentOhDear\Tests\Fixtures\OhDearFixtures;

it('renders and filters custom api monitor data', function () {
    config()->set('filament-oh-dear.api_token', 'test-token');

    app()->instance(OhDearDataRepository::class, new FakeOhDearDataRepository(
        user: OhDearFixtures::user(),
        monitors: OhDearFixtures::monitors(),
    ));

    Livewire::test(MonitorsPage::class)
        ->assertCanSeeTableRecords(['1', '2'])
        ->searchTable('api ping')
        ->assertCanSeeTableRecords(['2'])
        ->assertCanNotSeeTableRecords(['1'])
        ->searchTable(null)
        ->filterTable('result', 'warning')
        ->assertCanSeeTableRecords(['2'])
        ->assertCanNotSeeTableRecords(['1']);
});
