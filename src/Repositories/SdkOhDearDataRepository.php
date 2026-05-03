<?php

namespace Ziming\FilamentOhDear\Repositories;

use Carbon\CarbonInterface;
use OhDear\PhpSdk\Enums\CheckType;
use OhDear\PhpSdk\Enums\UptimeMetricsSplit;
use OhDear\PhpSdk\OhDear;
use Ziming\FilamentOhDear\Exceptions\MissingApiTokenException;
use Ziming\FilamentOhDear\Support\OhDearDataNormalizer;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class SdkOhDearDataRepository implements OhDearDataRepository
{
    /**
     * @var array<string, OhDear>
     */
    protected array $clients = [];

    public function __construct(
        protected OhDearDataNormalizer $normalizer,
    ) {}

    public function me(OhDearSettings $settings): UserViewModel
    {
        return $this->normalizer->normalizeUser($this->client($settings)->me());
    }

    public function monitors(OhDearSettings $settings): array
    {
        return array_map(
            fn ($monitor): MonitorViewModel => $this->normalizer->normalizeMonitor($monitor),
            [...$this->client($settings)->monitors($settings->teamId)],
        );
    }

    public function monitor(OhDearSettings $settings, int $monitorId): MonitorViewModel
    {
        return $this->normalizer->normalizeMonitor($this->client($settings)->monitor($monitorId));
    }

    public function checkSummary(OhDearSettings $settings, int $monitorId, string $type): CheckSummaryViewModel
    {
        $checkType = CheckType::from($type);

        return $this->normalizer->normalizeCheckSummary(
            $type,
            $this->client($settings)->checkSummary($monitorId, $checkType),
        );
    }

    public function performanceMetrics(OhDearSettings $settings, int $monitorId, string $monitorType, CarbonInterface $start, CarbonInterface $end): array
    {
        $client = $this->client($settings);
        $startDate = $start->toDateTimeString();
        $endDate = $end->toDateTimeString();

        return match ($monitorType) {
            'http' => array_map(
                fn ($metric): PerformanceMetricViewModel => $this->normalizer->normalizeHttpMetric($metric),
                $client->httpUptimeMetrics($monitorId, $startDate, $endDate, UptimeMetricsSplit::Hour),
            ),
            'ping' => array_map(
                fn ($metric): PerformanceMetricViewModel => $this->normalizer->normalizePingMetric($metric),
                $client->pingUptimeMetrics($monitorId, $startDate, $endDate, UptimeMetricsSplit::Hour),
            ),
            'tcp' => array_map(
                fn ($metric): PerformanceMetricViewModel => $this->normalizer->normalizeTcpMetric($metric),
                $client->tcpUptimeMetrics($monitorId, $startDate, $endDate, UptimeMetricsSplit::Hour),
            ),
            default => [],
        };
    }

    public function certificateHealth(OhDearSettings $settings, int $monitorId): CertificateHealthViewModel
    {
        return $this->normalizer->normalizeCertificateHealth(
            $this->client($settings)->certificateHealth($monitorId),
        );
    }

    public function brokenLinks(OhDearSettings $settings, int $monitorId): array
    {
        return array_map(
            fn ($brokenLink): BrokenLinkViewModel => $this->normalizer->normalizeBrokenLink($brokenLink),
            [...$this->client($settings)->brokenLinks($monitorId)],
        );
    }

    public function downtime(OhDearSettings $settings, int $monitorId, CarbonInterface $start, CarbonInterface $end): array
    {
        return array_map(
            fn ($downtime): DowntimePeriodViewModel => $this->normalizer->normalizeDowntime($downtime),
            $this->client($settings)->downtime($monitorId, $start->toDateTimeString(), $end->toDateTimeString()),
        );
    }

    protected function client(OhDearSettings $settings): OhDear
    {
        if (! $settings->isConfigured()) {
            throw MissingApiTokenException::make();
        }

        $key = sha1((string) $settings->apiToken);

        return $this->clients[$key] ??= new OhDear($settings->apiToken);
    }
}
