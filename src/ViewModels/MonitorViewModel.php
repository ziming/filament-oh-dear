<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final readonly class MonitorViewModel
{
    /**
     * @param  array<int, string>  $tags
     * @param  array<string, CheckSummaryViewModel>  $checks
     */
    public function __construct(
        public int $id,
        public ?int $teamId,
        public string $type,
        public string $url,
        public bool $usesHttps,
        public ?string $label,
        public string $group,
        public array $tags,
        public ?string $description,
        public ?string $notes,
        public ?string $latestRunAt,
        public ?string $result,
        public array $checks,
        public bool $supportsPerformanceMetrics,
        public bool $supportsCertificateHealth,
        public bool $supportsBrokenLinks,
    ) {}

    public function displayName(): string
    {
        return filled($this->label) ? (string) $this->label : $this->url;
    }

    public function groupLabel(): string
    {
        return filled($this->group) ? $this->group : 'Ungrouped';
    }

    public function tagsLabel(): string
    {
        return $this->tags !== [] ? implode(', ', $this->tags) : 'None';
    }

    public function latestRunDisplay(): string
    {
        if (! $this->latestRunAt) {
            return 'Never';
        }

        return CarbonImmutable::parse($this->latestRunAt)->toDayDateTimeString();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'http' => 'HTTP',
            'tcp' => 'TCP',
            default => Str::headline($this->type),
        };
    }

    public function resultLabel(): string
    {
        return match ($this->result) {
            'succeeded' => 'Healthy',
            'warning' => 'Warning',
            'failed' => 'Failed',
            'errored-or-timed-out' => 'Timed out',
            'pending' => 'Pending',
            default => 'Unknown',
        };
    }

    public function resultColor(): string
    {
        return match ($this->result) {
            'succeeded' => 'success',
            'warning' => 'warning',
            'failed', 'errored-or-timed-out' => 'danger',
            default => 'gray',
        };
    }

    public function isHealthy(): bool
    {
        return $this->result === 'succeeded';
    }

    public function hasIssues(): bool
    {
        return ! in_array($this->result, ['succeeded', null], true);
    }

    public function hasCertificateIssues(): bool
    {
        $summary = $this->check('certificate_health');

        return $summary?->isProblematic() ?? false;
    }

    public function needsAttention(): bool
    {
        return $this->hasIssues() || $this->hasCertificateIssues();
    }

    public function issueSummary(): string
    {
        $problemSummaries = collect($this->checks)
            ->filter(static fn (CheckSummaryViewModel $summary): bool => $summary->isProblematic())
            ->map(static fn (CheckSummaryViewModel $summary): string => $summary->displaySummary())
            ->filter()
            ->values();

        if ($problemSummaries->isNotEmpty()) {
            return $problemSummaries->join(' | ');
        }

        return $this->resultLabel();
    }

    public function check(string $type): ?CheckSummaryViewModel
    {
        return $this->checks[$type] ?? null;
    }

    public function summaryFor(string $type, string $unavailable = 'Not enabled'): string
    {
        $summary = $this->check($type);

        if ($summary) {
            return $summary->displaySummary();
        }

        return $unavailable;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->displayName(),
            'label' => $this->label,
            'url' => $this->url,
            'type' => $this->type,
            'type_label' => $this->typeLabel(),
            'group' => $this->group,
            'group_label' => $this->groupLabel(),
            'tags' => $this->tags,
            'tags_label' => $this->tagsLabel(),
            'description' => $this->description,
            'notes' => $this->notes,
            'latest_run_at' => $this->latestRunAt,
            'latest_run_display' => $this->latestRunDisplay(),
            'result' => $this->result,
            'result_label' => $this->resultLabel(),
            'result_color' => $this->resultColor(),
            'is_healthy' => $this->isHealthy(),
            'has_issues' => $this->hasIssues(),
            'has_certificate_issues' => $this->hasCertificateIssues(),
            'issue_summary' => $this->issueSummary(),
            'uptime_summary' => $this->summaryFor('uptime'),
            'performance_summary' => $this->summaryFor('performance'),
            'certificate_summary' => $this->summaryFor('certificate_health'),
            'broken_links_summary' => $this->summaryFor('broken_links'),
            'supports_performance_metrics' => $this->supportsPerformanceMetrics,
            'supports_certificate_health' => $this->supportsCertificateHealth,
            'supports_broken_links' => $this->supportsBrokenLinks,
            'checks' => array_map(
                static fn (CheckSummaryViewModel $check): array => $check->toArray(),
                array_values($this->checks),
            ),
        ];
    }
}
