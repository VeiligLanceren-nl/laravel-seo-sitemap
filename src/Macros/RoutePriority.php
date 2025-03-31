<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;

class RoutePriority
{
    public static function register(): void
    {
        RoutingRoute::macro('priority', function (string $value) {
            /** @var RoutingRoute $this */
            $this->defaults['sitemap_priority'] = $value;
            return $this;
        });
    }
}