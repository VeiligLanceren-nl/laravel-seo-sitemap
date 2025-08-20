<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

class RouteSitemapIndex
{
    /**
     * Register the sitemapIndex macro on routes.
     *
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('sitemapIndex', function (string $index) {
            /** @var RoutingRoute $this */
            $existing = $this->defaults['sitemap'] ?? new RouteSitemapDefaults();
            $existing->enabled = true;
            $existing->index = $index;
            $this->defaults['sitemap'] = $existing;

            return $this;
        });
    }
}
