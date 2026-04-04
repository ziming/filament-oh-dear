<?php

use Ziming\FilamentOhDear\Pages\OverviewPage;

it('shows a setup empty state when the api token is missing', function () {
    config()->set('filament-oh-dear.api_token', null);

    $this->get(OverviewPage::getUrl())
        ->assertOk()
        ->assertSee('Finish the Oh Dear setup')
        ->assertSee('php artisan filament-oh-dear:verify');
});
