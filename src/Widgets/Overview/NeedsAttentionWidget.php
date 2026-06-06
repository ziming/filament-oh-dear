<?php

namespace Ziming\FilamentOhDear\Widgets\Overview;

use Filament\Widgets\Widget;

class NeedsAttentionWidget extends Widget
{
    protected static bool $isLazy = false;

    /** @var int | string | array<string, int | null> */
    protected int|string|array $columnSpan = 'full';

    // @phpstan-ignore-next-line
    protected string $view = 'filament-oh-dear::widgets.overview.needs-attention';

    /** @var array<string, mixed> */
    public array $overview = [];
}
