<?php

use Ziming\FilamentOhDear\FilamentOhDearPlugin;

it('prefers plugin overrides over package config defaults', function () {
    config()->set('filament-oh-dear', [
        'api_token' => 'config-token',
        'team_id' => 1,
        'monitor_ids' => [11, 12],
        'cache' => [
            'store' => 'array',
            'ttl' => 300,
        ],
        'navigation' => [
            'group' => 'Configured',
            'icon' => 'heroicon-o-bell',
            'sort' => 60,
        ],
    ]);

    $settings = FilamentOhDearPlugin::make()
        ->apiToken('plugin-token')
        ->teamId(10)
        ->monitorIds([99])
        ->cacheStore('redis')
        ->cacheTtl(0)
        ->navigationGroup('Ops')
        ->navigationIcon('heroicon-o-signal')
        ->navigationSort(40)
        ->getSettings();

    expect($settings->apiToken)->toBe('plugin-token')
        ->and($settings->teamId)->toBe(10)
        ->and($settings->monitorIds)->toBe([99])
        ->and($settings->cacheStore)->toBe('redis')
        ->and($settings->cacheTtl)->toBe(0)
        ->and($settings->navigationGroup)->toBe('Ops')
        ->and($settings->navigationIcon)->toBe('heroicon-o-signal')
        ->and($settings->navigationSort)->toBe(40);
});
