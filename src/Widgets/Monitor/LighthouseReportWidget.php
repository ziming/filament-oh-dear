<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class LighthouseReportWidget extends BaseMonitorWidget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.monitor.lighthouse-report';

    /** @var array<string, mixed>|null */
    public ?array $report = null;

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->report = $service->loadLatestLighthouseReportForMonitor($settings, $monitor);
    }
}
