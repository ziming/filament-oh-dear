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

    /**
     * @var array<string, mixed>|null
     */
    public ?array $detail = null;

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'oh-dear.monitors.view';
    }

    public function mount(int $monitor): void
    {
        $settings = $this->settings();

        if (! $settings->isConfigured()) {
            $this->requiresSetup = true;

            return;
        }

        try {
            $this->detail = $this->dataService()
                ->getMonitorDetail($settings, $monitor)
                ->toArray();
        } catch (MonitorOutOfScopeException) {
            abort(404);
        } catch (Throwable $exception) {
            $this->loadError = $exception->getMessage();
        }
    }

    public function getTitle(): string
    {
        return $this->detail['monitor']['display_name'] ?? parent::getTitle();
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
}
