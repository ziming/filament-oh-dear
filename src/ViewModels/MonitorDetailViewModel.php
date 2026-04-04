<?php

namespace Ziming\FilamentOhDear\ViewModels;

final readonly class MonitorDetailViewModel
{
    /**
     * @param  array<int, CheckSummaryViewModel>  $checkSummaries
     * @param  array<int, PerformanceMetricViewModel>  $metrics
     * @param  array<int, BrokenLinkViewModel>  $brokenLinks
     * @param  array<int, DowntimePeriodViewModel>  $downtimePeriods
     * @param  array<int, string>  $warnings
     */
    public function __construct(
        public MonitorViewModel $monitor,
        public array $checkSummaries,
        public array $metrics,
        public ?CertificateHealthViewModel $certificateHealth,
        public array $brokenLinks,
        public array $downtimePeriods,
        public array $warnings = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'monitor' => $this->monitor->toArray(),
            'check_summaries' => array_map(
                static fn (CheckSummaryViewModel $summary): array => $summary->toArray(),
                $this->checkSummaries,
            ),
            'metrics' => array_map(
                static fn (PerformanceMetricViewModel $metric): array => $metric->toArray(),
                $this->metrics,
            ),
            'certificate_health' => $this->certificateHealth?->toArray(),
            'broken_links' => array_map(
                static fn (BrokenLinkViewModel $brokenLink): array => $brokenLink->toArray(),
                $this->brokenLinks,
            ),
            'downtime_periods' => array_map(
                static fn (DowntimePeriodViewModel $downtime): array => $downtime->toArray(),
                $this->downtimePeriods,
            ),
            'warnings' => $this->warnings,
        ];
    }
}
