<?php

namespace Ziming\FilamentOhDear\Widgets\Overview;

use Filament\Widgets\Widget;

class MonitorsByGroupWidget extends Widget
{
    protected static bool $isLazy = false;

    /** @var int | string | array<string, int | null> */
    protected int|string|array $columnSpan = 'full';

    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.overview.monitors-by-group';

    /** @var array<string, mixed> */
    public array $overview = [];
}
