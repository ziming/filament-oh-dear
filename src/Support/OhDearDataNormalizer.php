<?php

namespace Ziming\FilamentOhDear\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OhDear\PhpSdk\Dto\BrokenLink;
use OhDear\PhpSdk\Dto\CertificateHealth;
use OhDear\PhpSdk\Dto\CheckSummary;
use OhDear\PhpSdk\Dto\DowntimePeriod;
use OhDear\PhpSdk\Dto\Monitor;
use OhDear\PhpSdk\Dto\UptimeMetric\HttpUptimeMetric;
use OhDear\PhpSdk\Dto\UptimeMetric\PingUptimeMetric;
use OhDear\PhpSdk\Dto\UptimeMetric\TcpUptimeMetric;
use OhDear\PhpSdk\Dto\User;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateCheckViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\TeamViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class OhDearDataNormalizer
{
    public function normalizeUser(User $user): UserViewModel
    {
        return new UserViewModel(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            photoUrl: $user->photoUrl,
            teams: array_map(fn (array $team): TeamViewModel => $this->normalizeTeam($team), $user->teams),
        );
    }

    /**
     * @param  array<string, mixed>  $team
     */
    public function normalizeTeam(array $team): TeamViewModel
    {
        return new TeamViewModel(
            id: (int) ($team['id'] ?? 0),
            name: (string) ($team['name'] ?? 'Unknown team'),
            role: filled($team['role'] ?? null) ? (string) $team['role'] : null,
        );
    }

    public function normalizeMonitor(Monitor $monitor): MonitorViewModel
    {
        $checks = collect($monitor->checks)
            ->filter(static fn (mixed $check): bool => is_array($check))
            ->map(function (array $check): ?CheckSummaryViewModel {
                $type = Arr::get($check, 'type');

                if (! is_string($type) || blank($type)) {
                    return null;
                }

                return $this->normalizeRawCheck($type, $check);
            })
            ->filter()
            ->keyBy(static fn (CheckSummaryViewModel $check): string => $check->type)
            ->all();

        return new MonitorViewModel(
            id: $monitor->id,
            teamId: $monitor->teamId,
            type: $monitor->type,
            url: $monitor->url,
            usesHttps: $monitor->usesHttps,
            label: $monitor->label,
            group: $monitor->groupName,
            tags: array_values(array_map('strval', $monitor->tags)),
            description: $monitor->description,
            notes: $monitor->notes,
            latestRunAt: $monitor->latestRunDate,
            result: $monitor->summarizedCheckResult,
            checks: $checks,
            supportsPerformanceMetrics: in_array($monitor->type, ['http', 'ping', 'tcp'], true),
            supportsCertificateHealth: $monitor->usesHttps || ! empty($monitor->certificateHealthCheckSettings) || isset($checks['certificate_health']),
            supportsBrokenLinks: $monitor->type === 'http' || ! empty($monitor->brokenLinksCheckSettings) || isset($checks['broken_links']),
        );
    }

    /**
     * @param  array<string, mixed>  $check
     */
    public function normalizeRawCheck(string $type, array $check): CheckSummaryViewModel
    {
        return new CheckSummaryViewModel(
            type: $type,
            result: $this->firstFilledString([
                Arr::get($check, 'result'),
                Arr::get($check, 'status'),
                Arr::get($check, 'check_result'),
                Arr::get($check, 'summarized_check_result'),
            ]),
            summary: $this->firstFilledString([
                Arr::get($check, 'summary'),
                Arr::get($check, 'message'),
                Arr::get($check, 'label'),
                Arr::get($check, 'meta.summary'),
            ]),
        );
    }

    public function normalizeCheckSummary(string $type, CheckSummary $summary): CheckSummaryViewModel
    {
        return new CheckSummaryViewModel(
            type: $type,
            result: $summary->result,
            summary: $summary->summary,
        );
    }

    public function normalizeCertificateHealth(CertificateHealth $health): CertificateHealthViewModel
    {
        $checks = array_map(
            fn (array $check): CertificateCheckViewModel => new CertificateCheckViewModel(
                type: (string) ($check['type'] ?? 'unknown'),
                passed: (bool) ($check['passed'] ?? false),
                message: $this->firstFilledString([
                    Arr::get($check, 'summary'),
                    Arr::get($check, 'message'),
                    Arr::get($check, 'description'),
                ]),
            ),
            $health->certificateChecks,
        );

        return new CertificateHealthViewModel(
            issuer: $health->getIssuer(),
            validFrom: $health->getValidFrom(),
            validUntil: $health->getValidUntil(),
            checks: $checks,
        );
    }

    public function normalizeBrokenLink(BrokenLink $brokenLink): BrokenLinkViewModel
    {
        return new BrokenLinkViewModel(
            statusCode: $brokenLink->statusCode,
            crawledUrl: $brokenLink->crawledUrl,
            foundOnUrl: $brokenLink->foundOnUrl,
            linkText: Str::of($brokenLink->linkText)->trim()->whenEmpty(fn (): string => '(No link text)')->toString(),
            internal: $brokenLink->internal,
        );
    }

    public function normalizeDowntime(DowntimePeriod $downtime): DowntimePeriodViewModel
    {
        return new DowntimePeriodViewModel(
            id: $downtime->id,
            startedAt: $downtime->startedAt,
            endedAt: $downtime->endedAt,
            notes: $downtime->notesHtml ?? $downtime->notesMarkdown,
        );
    }

    public function normalizeHttpMetric(HttpUptimeMetric $metric): PerformanceMetricViewModel
    {
        return new PerformanceMetricViewModel(
            timestamp: $metric->date,
            latencyMs: $metric->totalTimeInSeconds * 1000,
        );
    }

    public function normalizePingMetric(PingUptimeMetric $metric): PerformanceMetricViewModel
    {
        return new PerformanceMetricViewModel(
            timestamp: $metric->date,
            latencyMs: $metric->averageTimeInMs,
            uptimePercentage: $metric->uptimePercentage,
            downtimePercentage: $metric->downtimePercentage,
        );
    }

    public function normalizeTcpMetric(TcpUptimeMetric $metric): PerformanceMetricViewModel
    {
        return new PerformanceMetricViewModel(
            timestamp: $metric->date,
            latencyMs: $metric->timeToConnectInMs,
            uptimePercentage: $metric->uptimePercentage,
            downtimePercentage: $metric->downtimePercentage,
        );
    }

    protected function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        return filled($value) ? $value : null;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    protected function firstFilledString(array $values): ?string
    {
        foreach ($values as $value) {
            if ($value = $this->stringOrNull($value)) {
                return $value;
            }
        }

        return null;
    }
}
