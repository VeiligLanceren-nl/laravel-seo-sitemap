<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use DateTimeInterface;
use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

class RouteLastmod
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('lastmod', function (string|DateTimeInterface $date) {
            /** @var RoutingRoute $this */
            $existing = $this->defaults['sitemap'] ?? new RouteSitemapDefaults();

            $existing->enabled = true;
            $existing->lastmod = $date instanceof DateTimeInterface
                ? $date->format('Y-m-d')
                : $date;

            $this->defaults['sitemap'] = $existing;

            return $this;
        });
    }
}
