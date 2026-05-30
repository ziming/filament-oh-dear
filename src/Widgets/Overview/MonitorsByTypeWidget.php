<?php

namespace Ziming\FilamentOhDear\Widgets\Overview;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitorsByTypeWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    /** @var array<string, mixed> */
    public array $overview = [];

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $byType = $this->overview['by_type'] ?? [];

        if ($byType === []) {
            return [
                Stat::make('No monitors', 0)
                    ->description('Add monitors in Oh Dear to populate this view')
                    ->color('gray'),
            ];
        }

        return collect($byType)
            ->map(static fn (int $count, string $type): Stat => Stat::make($type, $count)
                ->description('Monitors of this type')
                ->color('gray'))
            ->values()
            ->all();
    }
}
