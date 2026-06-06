<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class MonitorSummaryWidget extends BaseMonitorWidget
{
    /** @var view-string */
    protected string $view = 'filament-oh-dear::widgets.monitor.summary';

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        // The base widget already hydrated $this->monitor; nothing else to load.
    }
}
