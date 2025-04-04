<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class RouteChangefreq
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('changefreq', function (string|ChangeFrequency $changeFrequency) {
            /** @var RoutingRoute $this */
            $existing = $this->defaults['sitemap'] ?? new RouteSitemapDefaults();

            $existing->enabled = true;
            $existing->changefreq = $changeFrequency instanceof ChangeFrequency
                ? $changeFrequency
                : ChangeFrequency::from($changeFrequency);

            $this->defaults['sitemap'] = $existing;

            return $this;
        });
    }
}