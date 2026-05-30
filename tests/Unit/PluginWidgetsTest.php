<?php

use Ziming\FilamentOhDear\FilamentOhDearPlugin;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\Widgets\Monitor\BrokenLinksWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\CertificateHealthWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\CheckSummariesWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\DomainInfoWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\LighthouseReportWidget;
use Ziming\FilamentOhDear\Widgets\Monitor\MixedContentWidget;
use Ziming\FilamentOhDear\Widgets\Overview\MonitorsByGroupWidget;
use Ziming\FilamentOhDear\Widgets\Overview\NeedsAttentionWidget;
use Ziming\FilamentOhDear\Widgets\OverviewStatsWidget;

it('uses the default widget list when no overrides are provided', function () {
    $settings = FilamentOhDearPlugin::make()->getSettings();

    expect($settings->overviewWidgets)->toBe(OhDearSettings::defaultOverviewWidgets())
        ->and($settings->monitorWidgets)->toBe(OhDearSettings::defaultMonitorWidgets());
});

it('replaces the overview widget list with overviewWidgets', function () {
    $settings = FilamentOhDearPlugin::make()
        ->overviewWidgets([OverviewStatsWidget::class])
        ->getSettings();

    expect($settings->overviewWidgets)->toBe([OverviewStatsWidget::class]);
});

it('appends widgets with addOverviewWidgets and removes them with removeOverviewWidgets', function () {
    $settings = FilamentOhDearPlugin::make()
        ->addOverviewWidgets([MonitorsByGroupWidget::class])
        ->removeOverviewWidgets(NeedsAttentionWidget::class)
        ->getSettings();

    expect($settings->overviewWidgets)
        ->toContain(OverviewStatsWidget::class, MonitorsByGroupWidget::class)
        ->not->toContain(NeedsAttentionWidget::class);
});

it('lets monitor widgets be reconfigured per plugin', function () {
    $settings = FilamentOhDearPlugin::make()
        ->monitorWidgets([
            CheckSummariesWidget::class,
            CertificateHealthWidget::class,
        ])
        ->addMonitorWidgets([LighthouseReportWidget::class, MixedContentWidget::class, DomainInfoWidget::class])
        ->removeMonitorWidgets(BrokenLinksWidget::class)
        ->getSettings();

    expect($settings->monitorWidgets)->toBe([
        CheckSummariesWidget::class,
        CertificateHealthWidget::class,
        LighthouseReportWidget::class,
        MixedContentWidget::class,
        DomainInfoWidget::class,
    ]);
});
