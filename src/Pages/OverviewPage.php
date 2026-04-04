<?php

namespace Ziming\FilamentOhDear\Pages;

use Throwable;

class OverviewPage extends BaseOhDearPage
{
    protected static ?string $title = 'Overview';

    protected static ?string $slug = 'oh-dear';

    protected string $view = 'filament-oh-dear::pages.overview';

    public bool $requiresSetup = false;

    public ?string $loadError = null;

    /**
     * @var array<string, mixed>
     */
    public array $overview = [];

    public function mount(): void
    {
        $settings = $this->settings();

        if (! $settings->isConfigured()) {
            $this->requiresSetup = true;

            return;
        }

        try {
            $this->overview = $this->dataService()->getOverview($settings);
        } catch (Throwable $exception) {
            $this->loadError = $exception->getMessage();
        }
    }
}
