<?php

namespace Ziming\FilamentOhDear\Widgets\Monitor;

use Filament\Widgets\Widget;
use Throwable;
use Ziming\FilamentOhDear\Support\OhDearDataService;
use Ziming\FilamentOhDear\Support\OhDearSettings;
use Ziming\FilamentOhDear\ViewModels\MonitorViewModel;

abstract class BaseMonitorWidget extends Widget
{
    protected static bool $isLazy = false;

    /** @var int | string | array<string, int | null> */
    protected int|string|array $columnSpan = 'full';

    public ?int $monitorId = null;

    public ?string $loadError = null;

    public bool $loaded = false;

    /** @var array<string, mixed>|null */
    public ?array $monitor = null;

    public function mount(): void
    {
        if ($this->monitorId === null) {
            return;
        }

        $settings = OhDearSettings::forCurrentPanel();

        if (! $settings->isConfigured()) {
            return;
        }

        try {
            $service = app(OhDearDataService::class);
            $monitor = $service->getMonitor($settings, $this->monitorId);
            $this->monitor = $monitor->toArray();
            $this->loadData($settings, $service, $monitor);
            $this->loaded = true;
        } catch (Throwable $exception) {
            $this->loadError = $exception->getMessage();
        }
    }

    abstract protected function loadData(OhDearSettings $settings, OhDearDataService $service, MonitorViewModel $monitor): void;
}
