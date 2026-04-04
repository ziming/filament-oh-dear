<?php

namespace Ziming\FilamentOhDear\Repositories;

use Carbon\CarbonInterface;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

interface OhDearDataRepository
{
    public function me(OhDearSettings $settings): UserViewModel;

    /**
     * @return array<int, MonitorViewModel>
     */
    public function monitors(OhDearSettings $settings): array;

    public function monitor(OhDearSettings $settings, int $monitorId): MonitorViewModel;

    public function checkSummary(OhDearSettings $settings, int $monitorId, string $type): CheckSummaryViewModel;

    /**
     * @return array<int, PerformanceMetricViewModel>
     */
    public function performanceMetrics(OhDearSettings $settings, int $monitorId, string $monitorType, CarbonInterface $start, CarbonInterface $end): array;

    public function certificateHealth(OhDearSettings $settings, int $monitorId): CertificateHealthViewModel;

    /**
     * @return array<int, BrokenLinkViewModel>
     */
    public function brokenLinks(OhDearSettings $settings, int $monitorId): array;

    /**
     * @return array<int, DowntimePeriodViewModel>
     */
    public function downtime(OhDearSettings $settings, int $monitorId, CarbonInterface $start, CarbonInterface $end): array;
}
