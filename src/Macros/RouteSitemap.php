<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

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
                $url = Url::make(url($route->uri()));

                if (isset($route->defaults['sitemap_priority'])) {
                    $url->priority((float) $route->defaults['sitemap_priority']);
                }

                if (isset($route->defaults['sitemap_changefreq'])) {
                    $url->changefreq(ChangeFrequency::from($route->defaults['sitemap_changefreq']));
                }

                return $url;
            })
            ->values();
    }
}
