<?php

namespace Ziming\FilamentOhDear\Tests\Fixtures;

use Ziming\FilamentOhDear\ViewModels\BrokenLinkViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateCheckViewModel;
use Ziming\FilamentOhDear\ViewModels\CertificateHealthViewModel;
use Ziming\FilamentOhDear\ViewModels\CheckSummaryViewModel;
use Ziming\FilamentOhDear\ViewModels\DowntimePeriodViewModel;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;
use Ziming\FilamentOhDear\ViewModels\PerformanceMetricViewModel;
use Ziming\FilamentOhDear\ViewModels\TeamViewModel;
use Ziming\FilamentOhDear\ViewModels\UserViewModel;

class OhDearFixtures
{
    public static function user(): UserViewModel
    {
        return new UserViewModel(
            id: 1,
            name: 'Jane Doe',
            email: 'jane@example.com',
            photoUrl: 'https://example.com/photo.jpg',
            teams: [
                new TeamViewModel(id: 10, name: 'Platform', role: 'owner'),
            ],
        );
    }

    /**
     * @return array<int, MonitorViewModel>
     */
    public static function monitors(): array
    {
        return [
            new MonitorViewModel(
                id: 1,
                teamId: 10,
                type: 'http',
                url: 'https://status.example.com',
                usesHttps: true,
                label: 'Primary Site',
                group: 'Production',
                tags: ['critical', 'web'],
                description: null,
                notes: null,
                latestRunAt: '2026-04-03 08:30:00',
                result: 'succeeded',
                checks: [
                    'uptime' => new CheckSummaryViewModel('uptime', 'succeeded', 'Everything looks good.'),
                    'performance' => new CheckSummaryViewModel('performance', 'succeeded', 'Average response time 210 ms.'),
                    'certificate_health' => new CheckSummaryViewModel('certificate_health', 'succeeded', 'Certificate is valid.'),
                    'broken_links' => new CheckSummaryViewModel('broken_links', 'succeeded', 'No broken links found.'),
                ],
                supportsPerformanceMetrics: true,
                supportsCertificateHealth: true,
                supportsBrokenLinks: true,
            ),
            new MonitorViewModel(
                id: 2,
                teamId: 10,
                type: 'ping',
                url: 'https://api.example.com',
                usesHttps: false,
                label: 'API Ping',
                group: 'Production',
                tags: ['api'],
                description: null,
                notes: null,
                latestRunAt: '2026-04-03 08:28:00',
                result: 'warning',
                checks: [
                    'uptime' => new CheckSummaryViewModel('uptime', 'warning', 'Packet loss detected.'),
                    'performance' => new CheckSummaryViewModel('performance', 'warning', 'Latency increased to 350 ms.'),
                ],
                supportsPerformanceMetrics: true,
                supportsCertificateHealth: false,
                supportsBrokenLinks: false,
            ),
        ];
    }

    /**
     * @return array<string, CheckSummaryViewModel>
     */
    public static function detailSummaries(): array
    {
        return [
            'uptime' => new CheckSummaryViewModel('uptime', 'succeeded', 'Uptime is stable.'),
            'performance' => new CheckSummaryViewModel('performance', 'succeeded', 'Average latency 210 ms.'),
            'certificate_health' => new CheckSummaryViewModel('certificate_health', 'succeeded', 'Certificate expires in 42 days.'),
            'broken_links' => new CheckSummaryViewModel('broken_links', 'succeeded', 'No broken links detected.'),
        ];
    }

    /**
     * @return array<int, PerformanceMetricViewModel>
     */
    public static function metrics(): array
    {
        return [
            new PerformanceMetricViewModel('2026-04-02 10:00:00', 190),
            new PerformanceMetricViewModel('2026-04-02 12:00:00', 210),
            new PerformanceMetricViewModel('2026-04-02 14:00:00', 205),
            new PerformanceMetricViewModel('2026-04-02 16:00:00', 225),
        ];
    }

    public static function certificateHealth(): CertificateHealthViewModel
    {
        return new CertificateHealthViewModel(
            issuer: 'Let\'s Encrypt',
            validFrom: '2026-03-10 00:00:00',
            validUntil: '2026-05-15 00:00:00',
            checks: [
                new CertificateCheckViewModel('valid', true, 'Certificate is valid.'),
                new CertificateCheckViewModel('expires-soon', true, 'Not expiring soon.'),
            ],
        );
    }

    /**
     * @return array<int, BrokenLinkViewModel>
     */
    public static function brokenLinks(): array
    {
        return [
            new BrokenLinkViewModel(
                statusCode: 404,
                crawledUrl: 'https://status.example.com/missing',
                foundOnUrl: 'https://status.example.com/blog',
                linkText: 'Read more',
                internal: true,
            ),
        ];
    }

    /**
     * @return array<int, DowntimePeriodViewModel>
     */
    public static function downtime(): array
    {
        return [
            new DowntimePeriodViewModel(
                id: 1,
                startedAt: '2026-03-30 03:00:00',
                endedAt: '2026-03-30 03:12:00',
                notes: 'Edge provider outage',
            ),
        ];
    }
}
