<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

class CertificateHealthWidget extends BaseMonitorWidget
{
    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.monitor.certificate-health';

    /** @var array<string, mixed>|null */
    public ?array $certificateHealth = null;

    protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void
    {
        $this->certificateHealth = $service->loadCertificateHealthForMonitor($settings, $monitor)?->toArray();
    }
}
