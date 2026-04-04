<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Carbon\CarbonImmutable;

final readonly class PerformanceMetricViewModel
{
    public function __construct(
        public string $timestamp,
        public float $latencyMs,
        public ?float $uptimePercentage = null,
        public ?float $downtimePercentage = null,
    ) {}

    public function displayTimestamp(): string
    {
        return CarbonImmutable::parse($this->timestamp)->format('M j, H:i');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'display_timestamp' => $this->displayTimestamp(),
            'latency_ms' => round($this->latencyMs, 2),
            'uptime_percentage' => $this->uptimePercentage !== null ? round($this->uptimePercentage, 2) : null,
            'downtime_percentage' => $this->downtimePercentage !== null ? round($this->downtimePercentage, 2) : null,
        ];
    }
}
