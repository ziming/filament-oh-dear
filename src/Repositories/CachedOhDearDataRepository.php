<?php

namespace Ziming\FilamentOhDear\Repositories;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\Support\ViewModelHydrator;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class CachedOhDearDataRepository implements OhDearDataRepository
{
    public function __construct(
        protected OhDearDataRepository $repository,
        protected CacheFactory $cache,
        protected ViewModelHydrator $hydrator = new ViewModelHydrator,
    ) {}

    public function me(OhDearSettings $settings): UserViewModel
    {
        return $this->hydrator->hydrateUser(
            $this->remember($settings, 'me', [], fn (): array => $this->hydrator->dehydrateUser($this->repository->me($settings))),
        );
    }

    public function monitors(OhDearSettings $settings): array
    {
        return array_map(
            fn (array $monitor): MonitorViewModel => $this->hydrator->hydrateMonitor($monitor),
            $this->remember($settings, 'monitors', [], fn (): array => array_map(
                fn (MonitorViewModel $monitor): array => $this->hydrator->dehydrateMonitor($monitor),
                $this->repository->monitors($settings),
            )),
        );
    }

    public function monitor(OhDearSettings $settings, int $monitorId): MonitorViewModel
    {
        return $this->hydrator->hydrateMonitor(
            $this->remember($settings, 'monitor', [$monitorId], fn (): array => $this->hydrator->dehydrateMonitor($this->repository->monitor($settings, $monitorId))),
        );
    }

    public function checkSummary(OhDearSettings $settings, int $monitorId, string $type): CheckSummaryViewModel
    {
        return $this->hydrator->hydrateCheckSummary(
            $this->remember($settings, 'check-summary', [$monitorId, $type], fn (): array => $this->hydrator->dehydrateCheckSummary($this->repository->checkSummary($settings, $monitorId, $type))),
        );
    }

    public function performanceMetrics(OhDearSettings $settings, int $monitorId, string $monitorType, CarbonInterface $start, CarbonInterface $end): array
    {
        return array_map(
            fn (array $metric) => $this->hydrator->hydratePerformanceMetric($metric),
            $this->remember(
                $settings,
                'performance-metrics',
                [$monitorId, $monitorType, $start->toIso8601String(), $end->toIso8601String()],
                fn (): array => array_map(
                    fn (PerformanceMetricViewModel $metric): array => $this->hydrator->dehydratePerformanceMetric($metric),
                    $this->repository->performanceMetrics($settings, $monitorId, $monitorType, $start, $end),
                ),
            ),
        );
    }

    public function certificateHealth(OhDearSettings $settings, int $monitorId): CertificateHealthViewModel
    {
        return $this->hydrator->hydrateCertificateHealth(
            $this->remember($settings, 'certificate-health', [$monitorId], fn (): array => $this->hydrator->dehydrateCertificateHealth($this->repository->certificateHealth($settings, $monitorId))),
        );
    }

    public function brokenLinks(OhDearSettings $settings, int $monitorId): array
    {
        return array_map(
            fn (array $brokenLink) => $this->hydrator->hydrateBrokenLink($brokenLink),
            $this->remember($settings, 'broken-links', [$monitorId], fn (): array => array_map(
                fn (BrokenLinkViewModel $brokenLink): array => $this->hydrator->dehydrateBrokenLink($brokenLink),
                $this->repository->brokenLinks($settings, $monitorId),
            )),
        );
    }

    public function downtime(OhDearSettings $settings, int $monitorId, CarbonInterface $start, CarbonInterface $end): array
    {
        return array_map(
            fn (array $downtime) => $this->hydrator->hydrateDowntimePeriod($downtime),
            $this->remember(
                $settings,
                'downtime',
                [$monitorId, $start->toIso8601String(), $end->toIso8601String()],
                fn (): array => array_map(
                    fn (DowntimePeriodViewModel $downtime): array => $this->hydrator->dehydrateDowntimePeriod($downtime),
                    $this->repository->downtime($settings, $monitorId, $start, $end),
                ),
            ),
        );
    }

    /**
     * @param  array<int, mixed>  $arguments
     */
    public function makeCacheKey(string $method, OhDearSettings $settings, array $arguments = []): string
    {
        return 'filament-oh-dear:'.$method.':'.sha1(json_encode([
            'settings' => $settings->cacheContext(),
            'arguments' => $arguments,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @template TValue
     *
     * @param  array<int, mixed>  $arguments
     * @param  callable(): TValue  $callback
     * @return TValue
     */
    protected function remember(OhDearSettings $settings, string $method, array $arguments, callable $callback): mixed
    {
        if ($settings->cacheTtl === 0) {
            return $callback();
        }

        return $this->cacheStore($settings)->remember(
            $this->makeCacheKey($method, $settings, $arguments),
            now()->addSeconds($settings->cacheTtl),
            $callback,
        );
    }

    protected function cacheStore(OhDearSettings $settings): CacheRepository
    {
        return $settings->cacheStore
            ? $this->cache->store($settings->cacheStore)
            : $this->cache->store();
    }
}
