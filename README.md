# Filament Oh Dear

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/filament-oh-dear.svg?style=flat-square)](https://packagist.org/packages/ziming/filament-oh-dear)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/filament-oh-dear/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/filament-oh-dear/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/filament-oh-dear/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/filament-oh-dear/actions?query=workflow%3A%22Fix+PHP+code+style+issues%22+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/filament-oh-dear.svg?style=flat-square)](https://packagist.org/packages/ziming/filament-oh-dear)

Read-only Oh Dear insights for Filament 4 and 5 panels. The plugin renders remote Oh Dear monitor data directly from the API, caches responses through Laravel, and avoids local sync tables or CRUD flows.

This package is still very much in progress, when it is ready I will tag a 1.0

## Support Us

You can donate to my github sponsor or use my referral link for Oh Dear so I get a small reward if you become a paid customer in the future. This comes at no extra cost to you and helps support my open source work.

https://ohdear.app/?via=filament-oh-dear


## Features

- `Overview` page composed of pluggable widgets — stats, needs-attention list, monitors-by-group breakdown
- `Monitors` page powered by Filament custom table data
- Hidden `Monitor details` page composed of pluggable per-monitor widgets: summary, check summaries, latency chart, certificate health, broken links, downtime, mixed content, latest Lighthouse report, application health checks, maintenance periods, domain info
- Add, remove, or replace any widget on either page via the plugin fluent API or package config
- Package config defaults with per-panel plugin overrides
- `php artisan filament-oh-dear:verify` for connection checks

## Installation

```bash
composer require ziming/filament-oh-dear
```

Publish the config if you want to customize defaults:

```bash
php artisan vendor:publish --tag="filament-oh-dear-config"
```

## Environment

```dotenv
OH_DEAR_API_TOKEN=
OH_DEAR_TEAM_ID=
OH_DEAR_MONITOR_IDS=1,2,3
OH_DEAR_CACHE_STORE=
OH_DEAR_CACHE_TTL=300
```

- `OH_DEAR_API_TOKEN` is required.
- `OH_DEAR_TEAM_ID` is optional. When omitted, the token default scope is used.
- `OH_DEAR_MONITOR_IDS` is optional. When set, only those monitors are shown and their order is preserved.
- `OH_DEAR_CACHE_TTL=0` disables caching.

## Register The Plugin

```php
use Ziming\FilamentOhDear\FilamentOhDearPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->plugin(FilamentOhDearPlugin::make());
}
```

## Per-Panel Overrides

```php
use Ziming\FilamentOhDear\FilamentOhDearPlugin;

FilamentOhDearPlugin::make()
    ->apiToken(fn (): string => decrypt(config('services.oh_dear.token')))
    ->teamId(10)
    ->monitorIds([12, 55, 89])
    ->cacheStore('redis')
    ->cacheTtl(120)
    ->navigationGroup('Observability')
    ->navigationIcon('heroicon-o-signal')
    ->navigationSort(40);
```

Precedence is always `plugin override > package config`.

## Configuring widgets

Both the `Overview` and `Monitor details` pages render a list of Filament widgets
that you can freely mix and match. Defaults come from
`Ziming\FilamentOhDear\Support\OhDearSettings::defaultOverviewWidgets()` and
`defaultMonitorWidgets()`.

```php
use Ziming\FilamentOhDear\FilamentOhDearPlugin;
use Ziming\FilamentOhDear\Widgets\Monitor\ApplicationHealthChecksWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\BrokenLinksWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\DomainInfoWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\LighthouseReportWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\MaintenancePeriodsWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\MixedContentWidget;
use Ziming\FilamentOhDear\Widgets\Overview\MonitorsByGroupWidget;
use Ziming\FilamentOhDear\Widgets\Overview\MonitorsByTypeWidget;

FilamentOhDearPlugin::make()
    // Append additional widgets to the defaults
    ->addOverviewWidgets([
        MonitorsByTypeWidget::class,
        MonitorsByGroupWidget::class,
    ])
    ->addMonitorWidgets([
        MixedContentWidget::class,
        LighthouseReportWidget::class,
        ApplicationHealthChecksWidget::class,
        MaintenancePeriodsWidget::class,
        DomainInfoWidget::class,
    ])
    // Or drop one you don't want
    ->removeMonitorWidgets(BrokenLinksWidget::class)
    // Or replace the entire list outright
    // ->overviewWidgets([OverviewStatsWidget::class])
    // ->monitorWidgets([MonitorSummaryWidget::class])
;
```

You can also point the package at your own widget classes — anything that
extends `BaseMonitorWidget` (with a public `int $monitorId` property) or any
Filament widget accepting an `overview` array prop will work.

### Available widgets

Overview (`Ziming\FilamentOhDear\Widgets\Overview\…` and
`Ziming\FilamentOhDear\Widgets\OverviewStatsWidget`):

- `OverviewStatsWidget` – totals and active issue counts
- `NeedsAttentionWidget` – list of monitors needing attention
- `MonitorsByGroupWidget` – healthy / issue breakdown per group
- `MonitorsByTypeWidget` – stat tiles per monitor type

Monitor (`Ziming\FilamentOhDear\Widgets\Monitor\…`):

- `MonitorSummaryWidget` – basic monitor info and status
- `CheckSummariesWidget` – per-check summaries
- `LatencyChartWidget` – 24-hour latency sparkline
- `CertificateHealthWidget` – TLS certificate details and checks
- `BrokenLinksWidget` – broken link findings
- `DowntimeWidget` – recent downtime periods
- `MixedContentWidget` – mixed-content findings (HTTPS pages loading HTTP assets)
- `LighthouseReportWidget` – latest Lighthouse scores and web vitals
- `ApplicationHealthChecksWidget` – application-reported health checks
- `MaintenancePeriodsWidget` – configured maintenance windows
- `DomainInfoWidget` – WHOIS / RDAP snapshot for the domain

## Verify Command

```bash
php artisan filament-oh-dear:verify
```

The command checks:

- an API token is configured
- `me()` succeeds
- the configured team scope is accessible when `team_id` is set
- each configured monitor is accessible when `monitor_ids` is set

## Support Matrix

- PHP: `^8.4`
- Filament: `^4 | ^5`
- Laravel / illuminate contracts: `^12 | ^13`
- Oh Dear SDK: `^4`

## Screenshots

- Overview page: placeholder
- Monitors table: placeholder
- Monitor detail page: placeholder

## Local Development

The repository ships with a Testbench workbench panel:

```bash
composer install
composer build
php vendor/bin/testbench serve
```

Then open `/admin/oh-dear`.

## Testing

```bash
composer test
composer analyse
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
