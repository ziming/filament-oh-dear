<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class CheckSummariesWidget extends BaseMonitorWidget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.monitor.check-summaries';

    /** @var array<int, array<string, mixed>> */
    public array $checkSummaries = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->checkSummaries = array_map(
            static fn (CheckSummaryViewModel $summary): array => $summary->toArray(),
            $service->loadCheckSummariesForMonitor($settings, $monitor),
        );
    }
}
