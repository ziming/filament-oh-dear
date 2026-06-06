<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class DomainInfoWidget extends BaseMonitorWidget
{
    /** @var view-string */
    protected string $view = 'filament-oh-dear::widgets.monitor.domain-info';

    /** @var array<string, mixed>|null */
    public ?array $domain = null;

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->domain = $service->loadDomainForMonitor($settings, $monitor);
    }
}
