<?php

namespace Ziming\FilamentOhDear\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Throwable;
use Ziming\FilamentOhDear\Exceptions\MonitorOutOfScopeException;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorDetailViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;

class OhDearDataService
{
    public function __construct(
        protected OhDearDataRepository $repository,
    ) {}

    /**
     * @return Collection<int, MonitorViewModel>
     */
    public function getScopedMonitors(OhDearSettings $settings): Collection
    {
        if ($settings->hasScopedMonitorIds()) {
            return collect($settings->monitorIds)
                ->map(fn (int $monitorId): MonitorViewModel => $this->repository->monitor($settings, $monitorId));
        }

        return collect($this->repository->monitors($settings));
    }

    public function getMonitor(OhDearSettings $settings, int $monitorId): MonitorViewModel
    {
        if ($settings->hasScopedMonitorIds()) {
            if (! in_array($monitorId, $settings->monitorIds, true)) {
                throw MonitorOutOfScopeException::forMonitor($monitorId);
            }

            return $this->repository->monitor($settings, $monitorId);
        }

        return $this->getScopedMonitors($settings)
            ->first(fn (MonitorViewModel $monitor): bool => $monitor->id === $monitorId)
            ?? throw MonitorOutOfScopeException::forMonitor($monitorId);
    }

    /**
     * @return array<string, mixed>
     */
    public function getOverview(OhDearSettings $settings): array
    {
        $monitors = $this->getScopedMonitors($settings);
        $needsAttention = $monitors
            ->filter(fn (MonitorViewModel $monitor): bool => $monitor->needsAttention())
            ->take(6)
            ->values();

        return [
            'total_monitors' => $monitors->count(),
            'healthy_monitors' => $monitors->filter(fn (MonitorViewModel $monitor): bool => $monitor->isHealthy())->count(),
            'monitors_with_active_issues' => $monitors->filter(fn (MonitorViewModel $monitor): bool => $monitor->hasIssues())->count(),
            'certificate_issues' => $monitors->filter(fn (MonitorViewModel $monitor): bool => $monitor->hasCertificateIssues())->count(),
            'needs_attention' => $needsAttention->map(fn (MonitorViewModel $monitor): array => $monitor->toArray())->all(),
            'by_type' => $monitors
                ->groupBy(fn (MonitorViewModel $monitor): string => $monitor->typeLabel())
                ->map(static fn (Collection $group): int => $group->count())
                ->sortKeys()
                ->all(),
            'by_group' => $monitors
                ->groupBy(fn (MonitorViewModel $monitor): string => $monitor->groupLabel())
                ->map(static fn (Collection $group): array => [
                    'total' => $group->count(),
                    'healthy' => $group->filter(fn (MonitorViewModel $monitor): bool => $monitor->isHealthy())->count(),
                    'issues' => $group->filter(fn (MonitorViewModel $monitor): bool => $monitor->needsAttention())->count(),
                ])
                ->sortKeys()
                ->all(),
        ];
    }

    /**
     * @return array<int, CheckSummaryViewModel>
     */
    public function loadCheckSummariesForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        $summaries = [];

        foreach ($this->detailCheckTypes($monitor) as $type) {
            try {
                $summaries[] = $this->repository->checkSummary($settings, $monitor->id, $type);
            } catch (Throwable) {
                if ($fallback = $monitor->check($type)) {
                    $summaries[] = $fallback;
                }
            }
        }

        return $summaries;
    }

    /**
     * @return array<int, PerformanceMetricViewModel>
     */
    public function loadPerformanceMetricsForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        if (! $monitor->supportsPerformanceMetrics) {
            return [];
        }

        return $this->repository->performanceMetrics(
            $settings,
            $monitor->id,
            $monitor->type,
            CarbonImmutable::now()->subDay(),
            CarbonImmutable::now(),
        );
    }

    public function loadCertificateHealthForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): ?CertificateHealthViewModel
    {
        if (! $monitor->supportsCertificateHealth) {
            return null;
        }

        return $this->repository->certificateHealth($settings, $monitor->id);
    }

    /**
     * @return array<int, BrokenLinkViewModel>
     */
    public function loadBrokenLinksForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        if (! $monitor->supportsBrokenLinks) {
            return [];
        }

        return $this->repository->brokenLinks($settings, $monitor->id);
    }

    /**
     * @return array<int, DowntimePeriodViewModel>
     */
    public function loadDowntimeForMonitor(OhDearSettings $settings, MonitorViewModel $monitor, int $days = 30): array
    {
        return $this->repository->downtime(
            $settings,
            $monitor->id,
            CarbonImmutable::now()->subDays($days),
            CarbonImmutable::now(),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loadMixedContentForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        if ($monitor->type !== 'http') {
            return [];
        }

        return $this->repository->mixedContent($settings, $monitor->id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function loadLatestLighthouseReportForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): ?array
    {
        if ($monitor->type !== 'http') {
            return null;
        }

        return $this->repository->latestLighthouseReport($settings, $monitor->id);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loadApplicationHealthChecksForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        return $this->repository->applicationHealthChecks($settings, $monitor->id);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loadMaintenancePeriodsForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): array
    {
        return $this->repository->maintenancePeriods($settings, $monitor->id);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function loadDomainForMonitor(OhDearSettings $settings, MonitorViewModel $monitor): ?array
    {
        if ($monitor->type !== 'http') {
            return null;
        }

        return $this->repository->domain($settings, $monitor->id);
    }

    public function getMonitorDetail(OhDearSettings $settings, int $monitorId): MonitorDetailViewModel
    {
        $monitor = $this->getMonitor($settings, $monitorId);
        $warnings = [];
        $summaries = [];

        foreach ($this->detailCheckTypes($monitor) as $type) {
            try {
                $summaries[] = $this->repository->checkSummary($settings, $monitorId, $type);
            } catch (Throwable $exception) {
                if ($fallback = $monitor->check($type)) {
                    $summaries[] = $fallback;
                }

                $warnings[] = $this->warningMessage($type, $exception);
            }
        }

        $metrics = [];

        if ($monitor->supportsPerformanceMetrics) {
            try {
                $metrics = $this->repository->performanceMetrics(
                    $settings,
                    $monitorId,
                    $monitor->type,
                    CarbonImmutable::now()->subDay(),
                    CarbonImmutable::now(),
                );
            } catch (Throwable $exception) {
                $warnings[] = $this->warningMessage('performance metrics', $exception);
            }
        }

        $certificateHealth = null;

        if ($monitor->supportsCertificateHealth) {
            try {
                $certificateHealth = $this->repository->certificateHealth($settings, $monitorId);
            } catch (Throwable $exception) {
                $warnings[] = $this->warningMessage('certificate health', $exception);
            }
        }

        $brokenLinks = [];

        if ($monitor->supportsBrokenLinks) {
            try {
                $brokenLinks = $this->repository->brokenLinks($settings, $monitorId);
            } catch (Throwable $exception) {
                $warnings[] = $this->warningMessage('broken links', $exception);
            }
        }

        $downtimePeriods = [];

        try {
            $downtimePeriods = $this->repository->downtime(
                $settings,
                $monitorId,
                CarbonImmutable::now()->subDays(30),
                CarbonImmutable::now(),
            );
        } catch (Throwable $exception) {
            $warnings[] = $this->warningMessage('downtime history', $exception);
        }

        return new MonitorDetailViewModel(
            monitor: $monitor,
            checkSummaries: $summaries,
            metrics: $metrics,
            certificateHealth: $certificateHealth,
            brokenLinks: $brokenLinks,
            downtimePeriods: $downtimePeriods,
            warnings: $warnings,
        );
    }

    /**
     * @return Collection<string, array<string, mixed>>
     */
    public function getMonitorTableRows(OhDearSettings $settings): Collection
    {
        /** @var Collection<string, array<string, mixed>> $rows */
        $rows = $this->getScopedMonitors($settings)
            ->mapWithKeys(fn (MonitorViewModel $monitor): array => [(string) $monitor->id => $monitor->toArray()]);

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    public function getMonitorGroups(OhDearSettings $settings): array
    {
        return $this->getScopedMonitors($settings)
            ->map(fn (MonitorViewModel $monitor): string => $monitor->groupLabel())
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function detailCheckTypes(MonitorViewModel $monitor): array
    {
        $types = ['uptime'];

        if ($monitor->supportsPerformanceMetrics) {
            $types[] = 'performance';
        }

        if ($monitor->supportsCertificateHealth) {
            $types[] = 'certificate_health';
        }

        if ($monitor->supportsBrokenLinks) {
            $types[] = 'broken_links';
        }

        return $types;
    }

    protected function warningMessage(string $section, Throwable $exception): string
    {
        return 'Unable to load '.$section.': '.$exception->getMessage();
    }
}
