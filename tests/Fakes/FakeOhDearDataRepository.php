<?php

namespace Ziming\FilamentOhDear\Tests\Fakes;

use Carbon\CarbonInterface;
use RuntimeException;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class FakeOhDearDataRepository implements OhDearDataRepository
{
    /**
     * @param  array<int, MonitorViewModel>  $monitors
     * @param  array<string, CheckSummaryViewModel>  $checkSummaries
     * @param  array<int, PerformanceMetricViewModel>  $metrics
     * @param  array<int, BrokenLinkViewModel>  $brokenLinks
     * @param  array<int, DowntimePeriodViewModel>  $downtime
     */
    public function __construct(
        public UserViewModel $user,
        public array $monitors = [],
        public array $checkSummaries = [],
        public array $metrics = [],
        public ?CertificateHealthViewModel $certificateHealth = null,
        public array $brokenLinks = [],
        public array $downtime = [],
    ) {}

    public int $monitorCalls = 0;

    public function me(OhDearSettings $settings): UserViewModel
    {
        return $this->user;
    }

    public function monitors(OhDearSettings $settings): array
    {
        return $this->monitors;
    }

    public function monitor(OhDearSettings $settings, int $monitorId): MonitorViewModel
    {
        $this->monitorCalls++;

        foreach ($this->monitors as $monitor) {
            if ($monitor->id === $monitorId) {
                return $monitor;
            }
        }

        throw new RuntimeException("Unknown monitor [{$monitorId}]");
    }

    public function checkSummary(OhDearSettings $settings, int $monitorId, string $type): CheckSummaryViewModel
    {
        return $this->checkSummaries[$type] ?? throw new RuntimeException("Missing summary [{$type}]");
    }

    public function performanceMetrics(OhDearSettings $settings, int $monitorId, string $monitorType, CarbonInterface $start, CarbonInterface $end): array
    {
        return $this->metrics;
    }

    public function certificateHealth(OhDearSettings $settings, int $monitorId): CertificateHealthViewModel
    {
        return $this->certificateHealth ?? throw new RuntimeException('Missing certificate health');
    }

    public function brokenLinks(OhDearSettings $settings, int $monitorId): array
    {
        return $this->brokenLinks;
    }

    public function downtime(OhDearSettings $settings, int $monitorId, CarbonInterface $start, CarbonInterface $end): array
    {
        return $this->downtime;
    }
}
