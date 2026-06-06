<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class BrokenLinksWidget extends BaseMonitorWidget
{
    /** @var view-string */
    protected string $view = 'filament-oh-dear::widgets.monitor.broken-links';

    /** @var array<int, array<string, mixed>> */
    public array $brokenLinks = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->brokenLinks = array_map(
            static fn (BrokenLinkViewModel $link): array => $link->toArray(),
            $service->loadBrokenLinksForMonitor($settings, $monitor),
        );
    }
}
