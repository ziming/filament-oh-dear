<?php

namespace Ziming\FilamentOhDear;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Ziming\FilamentOhDear\Commands\VerifyFilamentOhDearCommand;
use Ziming\FilamentOhDear\Repositories\CachedOhDearDataRepository;
use Ziming\FilamentOhDear\Repositories\OhDearDataRepository;
use Ziming\FilamentOhDear\Repositories\SdkOhDearDataRepository;
use Ziming\FilamentOhDear\Support\OhDearDataNormalizer;
use Ziming\FilamentOhDear\Support\OhDearDataService;

class FilamentOhDearServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-oh-dear')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(VerifyFilamentOhDearCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(OhDearDataNormalizer::class);

        $this->app->singleton(SdkOhDearDataRepository::class, function ($app): SdkOhDearDataRepository {
            return new SdkOhDearDataRepository($app->make(OhDearDataNormalizer::class));
        });

        $this->app->singleton(OhDearDataRepository::class, function ($app): CachedOhDearDataRepository {
            return new CachedOhDearDataRepository(
                $app->make(SdkOhDearDataRepository::class),
                $app->make(CacheFactory::class),
            );
        });

        $this->app->singleton(OhDearDataService::class, function ($app): OhDearDataService {
            return new OhDearDataService($app->make(OhDearDataRepository::class));
        });
    }
}
