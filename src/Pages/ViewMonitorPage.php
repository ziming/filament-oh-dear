<?php

namespace Ziming\FilamentOhDear\Pages;

use Filament\Panel;
use Throwable;
use Ziming\FilamentOhDear\Exceptions\MonitorOutOfScopeException;

class ViewMonitorPage extends BaseOhDearPage
{
    protected static ?string $title = 'Monitor details';

    protected static ?string $slug = 'oh-dear/monitors/{monitor}';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-oh-dear::pages.view-monitor';

    public bool $requiresSetup = false;

    public ?string $loadError = null;

    public ?int $monitorId = null;

    /** @var array<string, mixed>|null */
    public ?array $monitorSummary = null;

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'oh-dear.monitors.view';
    }

    public function mount(int $monitor): void
    {
        $this->monitorId = $monitor;

        $settings = $this->settings();

        if (! $settings->isConfigured()) {
            $this->requiresSetup = true;

            return;
        }

        try {
            $this->monitorSummary = $this->dataService()
                ->getMonitor($settings, $monitor)
                ->toArray();
        } catch (MonitorOutOfScopeException) {
            abort(404);
        } catch (Throwable $exception) {
            $this->loadError = $exception->getMessage();
        }
    }

    public function getTitle(): string
    {
        return $this->monitorSummary['display_name'] ?? parent::getTitle();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            OverviewPage::getUrl() => 'Oh Dear',
            MonitorsPage::getUrl() => 'Monitors',
            $this->getTitle(),
        ];
    }

    /**
     * @return array<int, class-string>
     */
    protected function getHeaderWidgets(): array
    {
        if ($this->requiresSetup || $this->loadError || $this->monitorId === null) {
            return [];
        }

        return $this->settings()->monitorWidgets;
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'monitorId' => $this->monitorId,
        ];
    }
}
