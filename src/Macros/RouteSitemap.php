<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Url;

class RouteSitemap
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('sitemap', function () {
            /** @var RoutingRoute $this */
            $this->defaults['sitemap'] = true;

            return $this;
        });
    }

    /**
     * Get all GET routes that are explicitly marked for the sitemap.
     *
     * @return Collection<Url>
     */
    public static function urls(): Collection
    {
        return collect(Route::getRoutes())
            ->filter(function (RoutingRoute $route) {
                return in_array('GET', $route->methods())
                    && ($route->defaults['sitemap'] ?? false);
            })
            ->map(function (RoutingRoute $route) {
                $priority = $route->defaults['sitemap_priority'] ?? 0.5;

                return Url::make(url($route->uri()))
                    ->priority((float) $priority);
            })
            ->values();
    }
}
