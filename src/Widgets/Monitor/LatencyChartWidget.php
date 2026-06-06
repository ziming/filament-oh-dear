<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;

class LatencyChartWidget extends BaseMonitorWidget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.monitor.latency-chart';

    /** @var array<int, array<string, mixed>> */
    public array $metrics = [];

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->metrics = array_map(
            static fn (PerformanceMetricViewModel $metric): array => $metric->toArray(),
            $service->loadPerformanceMetricsForMonitor($settings, $monitor),
        );
    }
}
