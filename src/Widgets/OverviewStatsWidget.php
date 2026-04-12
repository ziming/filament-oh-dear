<?php

namespace Ziming\FilamentOhDear\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    /**
     * @var array<string, mixed>
     */
    public array $overview = [];

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Total monitors', $this->overview['total_monitors'] ?? 0)
                ->color('gray')
                ->description('Scoped monitors in this panel'),
            Stat::make('Healthy monitors', $this->overview['healthy_monitors'] ?? 0)
                ->color('success')
                ->description('Currently reporting healthy'),
            Stat::make('Active issues', $this->overview['monitors_with_active_issues'] ?? 0)
                ->color('warning')
                ->description('Warnings, failures, or timeouts'),
            Stat::make('Certificate issues', $this->overview['certificate_issues'] ?? 0)
                ->color('danger')
                ->description('Problematic certificate checks'),
        ];
    }
}
