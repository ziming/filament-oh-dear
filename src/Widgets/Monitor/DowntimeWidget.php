<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class DowntimeWidget extends BaseMonitorWidget
{
    /** @var view-string */
    protected string $view = 'filament-oh-dear::widgets.monitor.downtime';

    /** @var array<int, array<string, mixed>> */
    public array $downtimePeriods = [];

    public int $days = 30;

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->downtimePeriods = array_map(
            static fn (DowntimePeriodViewModel $downtime): array => $downtime->toArray(),
            $service->loadDowntimeForMonitor($settings, $monitor, $this->days),
        );
    }
}
