<?php

namespace Ziming\FilamentOhDear\Pages;

use Filament\Pages\Page;
use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;

abstract class BaseOhDearPage extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return OhDearSettings::forCurrentPanel()->navigationGroup;
    }

    public static function getNavigationIcon(): ?string
    {
        return OhDearSettings::forCurrentPanel()->navigationIcon;
    }

    public static function getNavigationSort(): ?int
    {
        return OhDearSettings::forCurrentPanel()->navigationSort + static::getNavigationSortOffset();
    }

    protected static function getNavigationSortOffset(): int
    {
        return 0;
    }

    protected function settings(): OhDearSettings
    {
        return OhDearSettings::forCurrentPanel();
    }

    protected function dataService(): OhDearDataService
    {
        return app(OhDearDataService::class);
    }
}
