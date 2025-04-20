<?php

namespace Tests;

use Illuminate\Support\InteractsWithTime;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Filesystem\FilesystemServiceProvider;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteDynamic;
use VeiligLanceren\LaravelSeoSitemap\SitemapServiceProvider;

class TestCase extends BaseTestCase
{
    use InteractsWithTime;

    protected function setUp(): void
    {
        parent::setUp();

        RouteDynamic::register();
    }

    /**
     * @param $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            FilesystemServiceProvider::class,
            SitemapServiceProvider::class,
        ];
    }

    /**
     * @return void
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * @param $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('filesystems.default', 'public');
        $app['config']->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ]);
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}