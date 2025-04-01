<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;

class RouteChangefreq
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('changefreq', function (string $value) {
            /** @var RoutingRoute $this */
            $this->defaults['sitemap_changefreq'] = $value;

            return $this;
        });
    }
}