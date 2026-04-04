<?php

namespace Ziming\FilamentOhDear\Support;

use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Ziming\FilamentOhDear\FilamentOhDearPlugin;

final readonly class OhDearSettings
{
    /**
     * @param  array<int>  $monitorIds
     */
    public function __construct(
        public ?string $apiToken,
        public ?int $teamId,
        public array $monitorIds,
        public ?string $cacheStore,
        public int $cacheTtl,
        public string $navigationGroup,
        public string $navigationIcon,
        public int $navigationSort,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config): self
    {
        $monitorIds = array_values(array_filter(array_map(
            static fn (mixed $monitorId): ?int => is_numeric($monitorId) ? (int) $monitorId : null,
            Arr::wrap($config['monitor_ids'] ?? []),
        )));

        $teamId = $config['team_id'] ?? null;

        return new self(
            apiToken: filled($config['api_token'] ?? null) ? (string) $config['api_token'] : null,
            teamId: is_numeric($teamId) ? (int) $teamId : null,
            monitorIds: $monitorIds,
            cacheStore: filled(data_get($config, 'cache.store')) ? (string) data_get($config, 'cache.store') : null,
            cacheTtl: max(0, (int) data_get($config, 'cache.ttl', 300)),
            navigationGroup: (string) data_get($config, 'navigation.group', 'Oh Dear'),
            navigationIcon: (string) data_get($config, 'navigation.icon', 'heroicon-o-signal'),
            navigationSort: (int) data_get($config, 'navigation.sort', 50),
        );
    }

    public static function forCurrentPanel(): self
    {
        try {
            $panel = Filament::getCurrentOrDefaultPanel();

            if ($panel?->hasPlugin(FilamentOhDearPlugin::ID)) {
                /** @var FilamentOhDearPlugin $plugin */
                $plugin = $panel->getPlugin(FilamentOhDearPlugin::ID);

                return $plugin->getSettings();
            }
        } catch (\Throwable) {
            // Fall back to package config when no panel is available.
        }

        return self::fromArray(config('filament-oh-dear', []));
    }

    public function isConfigured(): bool
    {
        return filled($this->apiToken);
    }

    public function hasScopedMonitorIds(): bool
    {
        return $this->monitorIds !== [];
    }

    /**
     * @return array<string, mixed>
     */
    public function cacheContext(): array
    {
        return [
            'api_token' => sha1((string) $this->apiToken),
            'team_id' => $this->teamId,
            'monitor_ids' => $this->monitorIds,
        ];
    }
}
