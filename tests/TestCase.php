<?php

namespace Ziming\FilamentOhDear\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\App\Providers\AdminPanelProvider;
use Ziming\FilamentOhDear\FilamentOhDearServiceProvider;

class TestCase extends Orchestra
{
    protected $enablesPackageDiscoveries = true;

    protected function getPackageProviders($app)
    {
        return [
            FilamentOhDearServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:QKFnPAa4wUuPxcb6aYjYzeJCG16ZKTBxBxWv2t6Ktno=');
        config()->set('cache.default', 'array');
        config()->set('session.driver', 'array');
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
