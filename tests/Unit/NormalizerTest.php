<?php

use OhDear\PhpSdk\Dto\Monitor;
use Ziming\FilamentOhDear\Support\OhDearDataNormalizer;

it('normalizes sdk monitor responses into package view models', function () {
    $monitor = new Monitor(
        id: 1,
        teamId: 10,
        type: 'http',
        url: 'https://status.example.com',
        usesHttps: true,
        sortUrl: 'status.example.com',
        label: 'Primary Site',
        groupName: 'Production',
        tags: ['critical'],
        latestRunDate: '2026-04-03 08:30:00',
        summarizedCheckResult: 'warning',
        checks: [
            [
                'type' => 'uptime',
                'result' => 'warning',
                'summary' => 'Intermittent failures detected.',
            ],
            [
                'type' => 'certificate_health',
                'result' => 'warning',
                'summary' => 'Certificate expires soon.',
            ],
        ],
        performanceCheckSettings: ['enabled' => true],
        brokenLinksCheckSettings: ['enabled' => true],
    );

    $normalized = (new OhDearDataNormalizer)->normalizeMonitor($monitor);

    expect($normalized->displayName())->toBe('Primary Site')
        ->and($normalized->hasIssues())->toBeTrue()
        ->and($normalized->hasCertificateIssues())->toBeTrue()
        ->and($normalized->summaryFor('uptime'))->toBe('Intermittent failures detected.')
        ->and($normalized->supportsBrokenLinks)->toBeTrue();
});
