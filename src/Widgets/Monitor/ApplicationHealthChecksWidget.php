<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class ApplicationHealthChecksWidget extends BaseMonitorWidget
{
    protected string $view = 'filament-oh-dear::widgets.monitor.application-health-checks';

    /** @var array<int, array<string, mixed>> */
    public array $checks = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->checks = $service->loadApplicationHealthChecksForMonitor($settings, $monitor);
    }
}
