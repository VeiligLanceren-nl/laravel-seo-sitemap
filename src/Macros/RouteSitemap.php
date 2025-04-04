<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

class RouteSitemap
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('sitemap', function (array $parameters = []) {
            /** @var RoutingRoute $this */
            $existing = $this->defaults['sitemap'] ?? new RouteSitemapDefaults();

            $existing->enabled = true;

            if (is_array($parameters)) {
                $existing->parameters = $parameters;
            }

            $this->defaults['sitemap'] = $existing;

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
            ->filter(function (RoutingRoute $route) {
                return in_array('GET', $route->methods())
                    && ($route->defaults['sitemap'] ?? null) instanceof RouteSitemapDefaults
                    && $route->defaults['sitemap']->enabled;
            })
            ->flatMap(function (RoutingRoute $route) {
                /** @var RouteSitemapDefaults $defaults */
                $defaults = $route->defaults['sitemap'];
                $uri = $route->uri();

                $combinations = [[]];
                foreach ($defaults->parameters as $key => $values) {
                    $combinations = collect($combinations)->flatMap(function ($combo) use ($key, $values) {
                        return collect($values)->map(fn ($val) => array_merge($combo, [$key => $val]));
                    })->all();
                }

                $combinations = count($combinations) ? $combinations : [[]];

                return collect($combinations)->map(function ($params) use ($uri, $defaults) {
                    $filledUri = $uri;
                    foreach ($params as $key => $value) {
                        $replacement = is_object($value) && method_exists($value, 'getRouteKey')
                            ? $value->getRouteKey()
                            : (string) $value;

                        $filledUri = str_replace("{{$key}}", $replacement, $filledUri);
                    }

                    $url = Url::make(url($filledUri));

                    if ($defaults->priority !== null) {
                        $url->priority($defaults->priority);
                    }

                    if ($defaults->changefreq !== null) {
                        $url->changefreq($defaults->changefreq);
                    }

                    return $url;
                });
            })
            ->values();
    }
}
