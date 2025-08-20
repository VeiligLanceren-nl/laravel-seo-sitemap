<?php

namespace VeiligLanceren\LaravelSeoSitemap;

use Illuminate\Support\ServiceProvider;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteDynamic;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RoutePriority;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteChangefreq;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemapIndex;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteImage;
use VeiligLanceren\LaravelSeoSitemap\Services\SitemapService;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemapUsing;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\InstallSitemap;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\TemplateSitemap;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\GenerateSitemap;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\UpdateUrlLastmod;

class SitemapServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sitemap.php', 'sitemap');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallSitemap::class,
                GenerateSitemap::class,
                TemplateSitemap::class,
                UpdateUrlLastmod::class,
            ]);
        }

        $this->app->singleton(SitemapService::class, fn () => new SitemapService(new Sitemap()));
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sitemap.php' => config_path('sitemap.php'),
        ], 'sitemap-config');

        if (is_dir(__DIR__ . '/../database/migrations')) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'sitemap-migration');
        }

        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/sitemap.php');
        }

        if (is_dir(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'sitemap');
        }

        RouteSitemap::register();
        RouteSitemapUsing::register();
        RoutePriority::register();
        RouteChangefreq::register();
        RouteDynamic::register();
        RouteImage::register();
        RouteSitemapIndex::register();
    }
}
