<?php

namespace VeiligLanceren\LaravelSeoSitemap;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RoutePriority;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteChangefreq;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\IndexNowPingService;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;
use VeiligLanceren\LaravelSeoSitemap\Services\SearchEnginePingService;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\GenerateSitemapCommand;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\UpdateUrlLastmodCommand;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\Services\SearchEnginePingServiceInterface;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected array $pingServices = [
        IndexNowPingService::class,
        GooglePingService::class,
    ];

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sitemap.php', 'sitemap');

        $this->commands([
            GenerateSitemapCommand::class,
            UpdateUrlLastmodCommand::class,
        ]);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->bindInterfaces();

        $this->publishes([
            __DIR__ . '/../config/sitemap.php' => config_path('sitemap.php'),
        ], 'sitemap-config');

        if (is_dir(__DIR__ . '/../database/migrations')) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'sitemap-migration');
        }

        $this->registerRoutes();
        $this->publishes([
            __DIR__ . '/../routes/sitemap.php' => base_path('routes/sitemap.php'),
        ], 'sitemap-routes');

        if (is_dir(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sitemap');
        }

        $this->registerMacros();
    }

    /**
     * Register package routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $routeFile = base_path('routes/sitemap.php');

        if (file_exists($routeFile)) {
            $this->loadRoutesFrom($routeFile);
        } elseif (file_exists(__DIR__ . '/../routes/sitemap.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/sitemap.php');
        }
    }

    /**
     * Register package macros
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        RouteSitemap::register();
        RoutePriority::register();
        RouteChangefreq::register();
    }

    /**
     * Bind interfaces to the relative classes
     *
     * @return void
     */
    protected function bindInterfaces(): void
    {
        $pingServices = config('sitemap.ping_services', []);

        foreach ($pingServices as $pingService) {
            $this->app->bind($pingService, $pingService);
        }

        $this->app->bind(SearchEnginePingServiceInterface::class, SearchEnginePingService::class);
        $this->app->singleton(SearchEnginePingServiceInterface::class, function (Application $app) {
            return new SearchEnginePingService(
                collect($this->pingServices)
                    ->map(fn ($service) => $app->make($service))
                    ->all()
            );
        });
    }
}
