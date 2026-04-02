<?php

namespace Ziming\FilamentOhDear;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziming\FilamentOhDear\Commands\FilamentOhDearCommand;

class FilamentOhDearServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-oh-dear')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_oh_dear_table')
            ->hasCommand(FilamentOhDearCommand::class);
    }
}
