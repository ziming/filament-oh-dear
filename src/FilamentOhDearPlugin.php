<?php

namespace Ziming\FilamentOhDear;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Ziming\FilamentOhDear\Pages\MonitorsPage;
use Ziming\FilamentOhDear\Pages\OverviewPage;
use Ziming\FilamentOhDear\Pages\ViewMonitorPage;
use Ziming\FilamentOhDear\Support\OhDearSettings;

final class FilamentOhDearPlugin implements Plugin
{
    use EvaluatesClosures;

    public const ID = 'filament-oh-dear';

    protected string|Closure|null $apiToken = null;

    protected int|Closure|null $teamId = null;

    protected array|Closure|null $monitorIds = null;

    protected string|Closure|null $cacheStore = null;

    protected int|Closure|null $cacheTtl = null;

    protected string|Closure|null $navigationGroup = null;

    protected string|Closure|null $navigationIcon = null;

    protected int|Closure|null $navigationSort = null;

    protected array|Closure|null $overviewWidgets = null;

    /** @var array<int, class-string> */
    protected array $overviewWidgetsToAppend = [];

    /** @var array<int, class-string> */
    protected array $overviewWidgetsToRemove = [];

    protected array|Closure|null $monitorWidgets = null;

    /** @var array<int, class-string> */
    protected array $monitorWidgetsToAppend = [];

    /** @var array<int, class-string> */
    protected array $monitorWidgetsToRemove = [];

    public static function make(): static
    {
        return new self;
    }

    public function apiToken(string|Closure|null $apiToken): static
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function teamId(int|Closure|null $teamId): static
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function monitorIds(array|Closure|null $monitorIds): static
    {
        $this->monitorIds = $monitorIds;

        return $this;
    }

    public function cacheStore(string|Closure|null $cacheStore): static
    {
        $this->cacheStore = $cacheStore;

        return $this;
    }

    public function cacheTtl(int|Closure|null $cacheTtl): static
    {
        $this->cacheTtl = $cacheTtl;

        return $this;
    }

    public function navigationGroup(string|Closure|null $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function navigationIcon(string|Closure|null $navigationIcon): static
    {
        $this->navigationIcon = $navigationIcon;

        return $this;
    }

    public function navigationSort(int|Closure|null $navigationSort): static
    {
        $this->navigationSort = $navigationSort;

        return $this;
    }

    /**
     * Replace the default list of widgets shown on the Overview page.
     *
     * @param  array<int, class-string>|Closure|null  $widgets
     */
    public function overviewWidgets(array|Closure|null $widgets): static
    {
        $this->overviewWidgets = $widgets;

        return $this;
    }

    /**
     * Append widgets to the existing Overview widget list.
     *
     * @param  array<int, class-string>|class-string  $widgets
     */
    public function addOverviewWidgets(array|string $widgets): static
    {
        $this->overviewWidgetsToAppend = [
            ...$this->overviewWidgetsToAppend,
            ...(array) $widgets,
        ];

        return $this;
    }

    /**
     * Remove widgets from the Overview widget list.
     *
     * @param  array<int, class-string>|class-string  $widgets
     */
    public function removeOverviewWidgets(array|string $widgets): static
    {
        $this->overviewWidgetsToRemove = [
            ...$this->overviewWidgetsToRemove,
            ...(array) $widgets,
        ];

        return $this;
    }

    /**
     * Replace the default list of widgets shown on the Monitor details page.
     *
     * @param  array<int, class-string>|Closure|null  $widgets
     */
    public function monitorWidgets(array|Closure|null $widgets): static
    {
        $this->monitorWidgets = $widgets;

        return $this;
    }

    /**
     * Append widgets to the existing Monitor widget list.
     *
     * @param  array<int, class-string>|class-string  $widgets
     */
    public function addMonitorWidgets(array|string $widgets): static
    {
        $this->monitorWidgetsToAppend = [
            ...$this->monitorWidgetsToAppend,
            ...(array) $widgets,
        ];

        return $this;
    }

    /**
     * Remove widgets from the Monitor widget list.
     *
     * @param  array<int, class-string>|class-string  $widgets
     */
    public function removeMonitorWidgets(array|string $widgets): static
    {
        $this->monitorWidgetsToRemove = [
            ...$this->monitorWidgetsToRemove,
            ...(array) $widgets,
        ];

        return $this;
    }

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            OverviewPage::class,
            MonitorsPage::class,
            ViewMonitorPage::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public function getSettings(): OhDearSettings
    {
        $defaults = OhDearSettings::fromArray(config('filament-oh-dear', []));

        return new OhDearSettings(
            apiToken: $this->resolveString($this->apiToken) ?? $defaults->apiToken,
            teamId: $this->resolveInt($this->teamId) ?? $defaults->teamId,
            monitorIds: $this->resolveMonitorIds($this->monitorIds) ?? $defaults->monitorIds,
            cacheStore: $this->resolveString($this->cacheStore) ?? $defaults->cacheStore,
            cacheTtl: $this->resolveInt($this->cacheTtl) ?? $defaults->cacheTtl,
            navigationGroup: $this->resolveString($this->navigationGroup) ?? $defaults->navigationGroup,
            navigationIcon: $this->resolveString($this->navigationIcon) ?? $defaults->navigationIcon,
            navigationSort: $this->resolveInt($this->navigationSort) ?? $defaults->navigationSort,
            overviewWidgets: $this->resolveWidgetList(
                $this->overviewWidgets,
                $this->overviewWidgetsToAppend,
                $this->overviewWidgetsToRemove,
                $defaults->overviewWidgets,
            ),
            monitorWidgets: $this->resolveWidgetList(
                $this->monitorWidgets,
                $this->monitorWidgetsToAppend,
                $this->monitorWidgetsToRemove,
                $defaults->monitorWidgets,
            ),
        );
    }

    protected function resolveString(string|Closure|null $value): ?string
    {
        $evaluated = $this->evaluate($value);

        return filled($evaluated) ? (string) $evaluated : null;
    }

    protected function resolveInt(int|Closure|null $value): ?int
    {
        $evaluated = $this->evaluate($value);

        return is_numeric($evaluated) ? (int) $evaluated : null;
    }

    /**
     * @return array<int>|null
     */
    protected function resolveMonitorIds(array|Closure|null $value): ?array
    {
        $evaluated = $this->evaluate($value);

        if ($evaluated === null) {
            return null;
        }

        return array_values(array_filter(array_map(
            static fn (mixed $monitorId): ?int => is_numeric($monitorId) ? (int) $monitorId : null,
            $evaluated,
        )));
    }

    /**
     * @param  array<int, class-string>  $append
     * @param  array<int, class-string>  $remove
     * @param  array<int, class-string>  $fallback
     * @return array<int, class-string>
     */
    protected function resolveWidgetList(array|Closure|null $value, array $append, array $remove, array $fallback): array
    {
        $evaluated = $this->evaluate($value);

        $base = is_array($evaluated)
            ? array_values(array_filter(
                $evaluated,
                static fn (mixed $widget): bool => is_string($widget) && class_exists($widget),
            ))
            : $fallback;

        $merged = array_values(array_unique([...$base, ...$append]));

        return array_values(array_diff($merged, $remove));
    }
}
