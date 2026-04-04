<?php

use Filament\Facades\Filament;
use Ziming\FilamentOhDear\FilamentOhDearPlugin;
use Ziming\FilamentOhDear\Pages\MonitorsPage;
use Ziming\FilamentOhDear\Pages\OverviewPage;
use Ziming\FilamentOhDear\Pages\ViewMonitorPage;

it('registers the plugin pages into the filament panel', function () {
    $panel = Filament::getPanel('admin');

    expect($panel->hasPlugin(FilamentOhDearPlugin::ID))->toBeTrue()
        ->and($panel->getPages())->toContain(
            OverviewPage::class,
            MonitorsPage::class,
            ViewMonitorPage::class,
        );
});
