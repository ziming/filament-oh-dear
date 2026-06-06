<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class MaintenancePeriodsWidget extends BaseMonitorWidget
{
    /** @var view-string */
    protected string $view = 'filament-oh-dear::widgets.monitor.maintenance-periods';

    /** @var array<int, array<string, mixed>> */
    public array $periods = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->periods = $service->loadMaintenancePeriodsForMonitor($settings, $monitor);
    }
}
