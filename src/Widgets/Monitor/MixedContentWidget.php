<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class MixedContentWidget extends BaseMonitorWidget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.monitor.mixed-content';

    /** @var array<int, array<string, mixed>> */
    public array $mixedContent = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->mixedContent = $service->loadMixedContentForMonitor($settings, $monitor);
    }
}
