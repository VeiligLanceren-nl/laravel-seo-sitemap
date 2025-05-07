<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Closure;
use Illuminate\Routing\Route as RoutingRoute;

class RouteSitemapUsing
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('sitemapUsing', function (string $class): RoutingRoute {
            /** @var RoutingRoute $this */
            $this->defaults['sitemap']          = true;
            $this->defaults['sitemap_generator'] = $class;

            return $this;
        });
    }
}