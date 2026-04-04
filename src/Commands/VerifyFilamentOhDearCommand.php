<?php

namespace Ziming\FilamentOhDear\Commands;

use Illuminate\Console\Command;
use Throwable;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearSettings;

class VerifyFilamentOhDearCommand extends Command
{
    protected $signature = 'filament-oh-dear:verify';

    protected $description = 'Verify the configured Oh Dear API token, team scope, and monitor access.';

    public function handle(OhDearDataRepository $repository): int
    {
        $settings = OhDearSettings::fromArray(config('filament-oh-dear', []));

        if (! $settings->isConfigured()) {
            $this->components->error('Missing configuration: set OH_DEAR_API_TOKEN before using the plugin.');
            $this->line('OH_DEAR_API_TOKEN is required.');
            $this->line('Optional keys: OH_DEAR_TEAM_ID, OH_DEAR_MONITOR_IDS, OH_DEAR_CACHE_STORE, OH_DEAR_CACHE_TTL');

            return self::FAILURE;
        }

        try {
            $user = $repository->me($settings);
            $this->components->info("Authenticated as {$user->name} <{$user->email}>.");

            if ($settings->teamId !== null) {
                $count = count($repository->monitors($settings));
                $this->components->info("Team scope OK for team [{$settings->teamId}] with {$count} accessible monitors.");
            } else {
                $this->line('No OH_DEAR_TEAM_ID configured. Using the token default scope.');
            }

            if ($settings->hasScopedMonitorIds()) {
                foreach ($settings->monitorIds as $monitorId) {
                    $monitor = $repository->monitor($settings, $monitorId);
                    $this->line("Monitor [{$monitorId}] OK: {$monitor->displayName()}");
                }
            } else {
                $this->line('No OH_DEAR_MONITOR_IDS configured. All accessible monitors remain in scope.');
            }
        } catch (Throwable $exception) {
            $this->components->error('Oh Dear verification failed.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Filament Oh Dear is configured correctly.');

        return self::SUCCESS;
    }
}
