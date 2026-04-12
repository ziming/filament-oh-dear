<?php

namespace Ziming\FilamentOhDear\Support;

use Illuminate\Support\Arr;
use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateCheckViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\TeamViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class ViewModelHydrator
{
    /**
     * @return array<string, mixed>
     */
    public function dehydrateUser(UserViewModel $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => $user->photoUrl,
            'teams' => array_map(
                fn (TeamViewModel $team): array => $this->dehydrateTeam($team),
                $user->teams,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateUser(array $payload): UserViewModel
    {
        return new UserViewModel(
            id: (int) $payload['id'],
            name: (string) $payload['name'],
            email: (string) $payload['email'],
            photoUrl: (string) $payload['photo_url'],
            teams: array_map(fn (array $team): TeamViewModel => $this->hydrateTeam($team), $this->arrayList($payload, 'teams')),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateTeam(TeamViewModel $team): array
    {
        return [
            'id' => $team->id,
            'name' => $team->name,
            'role' => $team->role,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateTeam(array $payload): TeamViewModel
    {
        return new TeamViewModel(
            id: (int) $payload['id'],
            name: (string) $payload['name'],
            role: isset($payload['role']) ? (is_string($payload['role']) ? $payload['role'] : null) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateMonitor(MonitorViewModel $monitor): array
    {
        return [
            'id' => $monitor->id,
            'team_id' => $monitor->teamId,
            'type' => $monitor->type,
            'url' => $monitor->url,
            'uses_https' => $monitor->usesHttps,
            'label' => $monitor->label,
            'group' => $monitor->group,
            'tags' => $monitor->tags,
            'description' => $monitor->description,
            'notes' => $monitor->notes,
            'latest_run_at' => $monitor->latestRunAt,
            'result' => $monitor->result,
            'checks' => array_map(
                fn (CheckSummaryViewModel $check): array => $this->dehydrateCheckSummary($check),
                array_values($monitor->checks),
            ),
            'supports_performance_metrics' => $monitor->supportsPerformanceMetrics,
            'supports_certificate_health' => $monitor->supportsCertificateHealth,
            'supports_broken_links' => $monitor->supportsBrokenLinks,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateMonitor(array $payload): MonitorViewModel
    {
        $checks = collect($this->arrayList($payload, 'checks'))
            ->map(fn (array $check): CheckSummaryViewModel => $this->hydrateCheckSummary($check))
            ->keyBy(fn (CheckSummaryViewModel $check): string => $check->type)
            ->all();

        return new MonitorViewModel(
            id: (int) $payload['id'],
            teamId: isset($payload['team_id']) && is_numeric($payload['team_id']) ? (int) $payload['team_id'] : null,
            type: (string) $payload['type'],
            url: (string) $payload['url'],
            usesHttps: (bool) $payload['uses_https'],
            label: isset($payload['label']) && is_string($payload['label']) ? $payload['label'] : null,
            group: (string) $payload['group'],
            tags: array_values(array_map('strval', $payload['tags'] ?? [])),
            description: isset($payload['description']) && is_string($payload['description']) ? $payload['description'] : null,
            notes: isset($payload['notes']) && is_string($payload['notes']) ? $payload['notes'] : null,
            latestRunAt: isset($payload['latest_run_at']) && is_string($payload['latest_run_at']) ? $payload['latest_run_at'] : null,
            result: isset($payload['result']) && is_string($payload['result']) ? $payload['result'] : null,
            checks: $checks,
            supportsPerformanceMetrics: (bool) $payload['supports_performance_metrics'],
            supportsCertificateHealth: (bool) $payload['supports_certificate_health'],
            supportsBrokenLinks: (bool) $payload['supports_broken_links'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateCheckSummary(CheckSummaryViewModel $summary): array
    {
        return [
            'type' => $summary->type,
            'result' => $summary->result,
            'summary' => $summary->summary,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateCheckSummary(array $payload): CheckSummaryViewModel
    {
        return new CheckSummaryViewModel(
            type: (string) $payload['type'],
            result: isset($payload['result']) && is_string($payload['result']) ? $payload['result'] : null,
            summary: isset($payload['summary']) && is_string($payload['summary']) ? $payload['summary'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateCertificateHealth(CertificateHealthViewModel $health): array
    {
        return [
            'issuer' => $health->issuer,
            'valid_from' => $health->validFrom,
            'valid_until' => $health->validUntil,
            'checks' => array_map(
                fn (CertificateCheckViewModel $check): array => $this->dehydrateCertificateCheck($check),
                $health->checks,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateCertificateHealth(array $payload): CertificateHealthViewModel
    {
        return new CertificateHealthViewModel(
            issuer: isset($payload['issuer']) && is_string($payload['issuer']) ? $payload['issuer'] : null,
            validFrom: isset($payload['valid_from']) && is_string($payload['valid_from']) ? $payload['valid_from'] : null,
            validUntil: isset($payload['valid_until']) && is_string($payload['valid_until']) ? $payload['valid_until'] : null,
            checks: array_map(fn (array $check): CertificateCheckViewModel => $this->hydrateCertificateCheck($check), $this->arrayList($payload, 'checks')),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateCertificateCheck(CertificateCheckViewModel $check): array
    {
        return [
            'type' => $check->type,
            'passed' => $check->passed,
            'message' => $check->message,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateCertificateCheck(array $payload): CertificateCheckViewModel
    {
        return new CertificateCheckViewModel(
            type: (string) $payload['type'],
            passed: (bool) $payload['passed'],
            message: isset($payload['message']) && is_string($payload['message']) ? $payload['message'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydratePerformanceMetric(PerformanceMetricViewModel $metric): array
    {
        return [
            'timestamp' => $metric->timestamp,
            'latency_ms' => $metric->latencyMs,
            'uptime_percentage' => $metric->uptimePercentage,
            'downtime_percentage' => $metric->downtimePercentage,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydratePerformanceMetric(array $payload): PerformanceMetricViewModel
    {
        return new PerformanceMetricViewModel(
            timestamp: (string) $payload['timestamp'],
            latencyMs: (float) $payload['latency_ms'],
            uptimePercentage: isset($payload['uptime_percentage']) && is_numeric($payload['uptime_percentage']) ? (float) $payload['uptime_percentage'] : null,
            downtimePercentage: isset($payload['downtime_percentage']) && is_numeric($payload['downtime_percentage']) ? (float) $payload['downtime_percentage'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateBrokenLink(BrokenLinkViewModel $brokenLink): array
    {
        return [
            'status_code' => $brokenLink->statusCode,
            'crawled_url' => $brokenLink->crawledUrl,
            'found_on_url' => $brokenLink->foundOnUrl,
            'link_text' => $brokenLink->linkText,
            'internal' => $brokenLink->internal,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateBrokenLink(array $payload): BrokenLinkViewModel
    {
        return new BrokenLinkViewModel(
            statusCode: isset($payload['status_code']) && is_numeric($payload['status_code']) ? (int) $payload['status_code'] : null,
            crawledUrl: (string) $payload['crawled_url'],
            foundOnUrl: (string) $payload['found_on_url'],
            linkText: (string) $payload['link_text'],
            internal: (bool) $payload['internal'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function dehydrateDowntimePeriod(DowntimePeriodViewModel $downtime): array
    {
        return [
            'id' => $downtime->id,
            'started_at' => $downtime->startedAt,
            'ended_at' => $downtime->endedAt,
            'notes' => $downtime->notes,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrateDowntimePeriod(array $payload): DowntimePeriodViewModel
    {
        return new DowntimePeriodViewModel(
            id: (int) $payload['id'],
            startedAt: (string) $payload['started_at'],
            endedAt: isset($payload['ended_at']) && is_string($payload['ended_at']) ? $payload['ended_at'] : null,
            notes: isset($payload['notes']) && is_string($payload['notes']) ? $payload['notes'] : null,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    protected function arrayList(array $payload, string $key): array
    {
        return array_values(array_filter(
            Arr::wrap($payload[$key] ?? []),
            static fn (mixed $item): bool => is_array($item),
        ));
    }
}
