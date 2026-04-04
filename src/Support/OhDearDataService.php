<?php

namespace Ziming\FilamentOhDear\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Throwable;
use Ziming\FilamentOhDear\Exceptions\MonitorOutOfScopeException;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\ViewModels\MonitorDetailViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

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
        ];
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
     * @return Collection<int|string, array<string, mixed>>
     */
    public function getMonitorTableRows(OhDearSettings $settings): Collection
    {
        return $this->getScopedMonitors($settings)
            ->mapWithKeys(fn (MonitorViewModel $monitor): array => [(string) $monitor->id => $monitor->toArray()]);
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
