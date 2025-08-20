<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRoute;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItemTemplate as TemplateContract;

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
     * Get all GET routes that are explicitly marked for the sitemap
     * either through ->sitemap() or ->dynamic().
     *
     * @return Collection<Url>
     */
    public static function urls(): Collection
    {
        return collect(Route::getRoutes())
            ->filter(function (RoutingRoute $route) {
                return in_array('GET', $route->methods());
            })
            ->flatMap(function (RoutingRoute $route) {
                $urls = collect();

                if ($template = $route->defaults['sitemap_generator'] ?? null) {
                    $defaults = $route->defaults['sitemap'] ?? null;

                    return static::generateFromTemplate(
                        $route,
                        $template,
                        $defaults instanceof RouteSitemapDefaults ? $defaults : null
                    );
                }

                if (
                    ($route->defaults['sitemap'] ?? null) instanceof RouteSitemapDefaults &&
                    $route->defaults['sitemap']->enabled
                ) {
                    /** @var RouteSitemapDefaults $defaults */
                    $defaults = $route->defaults['sitemap'];
                    $uri = $route->uri();

                    if (is_callable($defaults->parameters)) {
                        $parameterSets = call_user_func($defaults->parameters);

                        return collect($parameterSets)->map(fn ($params) =>
                            static::buildUrlFromParams($uri, $params, $defaults)
                        );
                    }

                    $combinations = [[]];

                    foreach ($defaults->parameters as $key => $values) {
                        $combinations = collect($combinations)->flatMap(function ($combo) use ($key, $values) {
                            return collect($values)->map(fn ($val) => array_merge($combo, [$key => $val]));
                        })->all();
                    }

                    if (empty($combinations)) {
                        $combinations = [[]];
                    }

                    $urls = collect($combinations)
                        ->map(fn ($params) => static::buildUrlFromParams($uri, $params, $defaults))
                        ->filter(fn (Url $url) => ! str_contains($url->toArray()['loc'], '{'));
                }

                if (isset($route->defaults['sitemap.dynamic']) && is_callable($route->defaults['sitemap.dynamic'])) {
                    $callback = $route->defaults['sitemap.dynamic'];
                    $result = $callback();

                    $urlGenerator = function (array $params) use ($route): Url {
                        $defaults = $route->defaults['sitemap'] ?? null;

                        $url = Url::make(route($route->getName(), $params));

                        if ($defaults instanceof RouteSitemapDefaults) {
                            if ($defaults->priority !== null) {
                                $url->priority($defaults->priority);
                            }

                            if ($defaults->changefreq !== null) {
                                $url->changefreq($defaults->changefreq);
                            }

                            if ($defaults->lastmod !== null) {
                                $url->lastmod($defaults->lastmod);
                            }

                            if ($defaults->index !== null) {
                                $url->index($defaults->index);
                            }
                        }

                        return $url;
                    };

                    if ($result instanceof DynamicRoute) {
                        return $urls->merge(
                            $result->parameters()->map($urlGenerator)
                        );
                    }

                    return $urls->merge(
                        collect($result)->map($urlGenerator)
                    );
                }

                return $urls;
            })
            ->values();
    }

    /**
     * @param string $uri
     * @param array<string, mixed> $params
     * @param RouteSitemapDefaults $defaults
     * @return Url
     */
    protected static function buildUrlFromParams(string $uri, array $params, RouteSitemapDefaults $defaults): Url
    {
        foreach ($params as $key => $value) {
            $replacement = is_object($value) && method_exists($value, 'getRouteKey')
                ? $value->getRouteKey()
                : (string) $value;

            $uri = str_replace("{{$key}}", $replacement, $uri);
        }

        $url = Url::make(url($uri));

        if ($defaults->priority !== null) {
            $url->priority($defaults->priority);
        }

        if ($defaults->changefreq !== null) {
            $url->changefreq($defaults->changefreq);
        }

        if ($defaults->lastmod !== null) {
            $url->lastmod($defaults->lastmod);
        }

        if ($defaults->index !== null) {
            $url->index($defaults->index);
        }

        return $url;
    }

    /**
     * @param RoutingRoute $route
     * @param class-string $class
     * @return Collection<Url>
     */
    private static function generateFromTemplate(
        RoutingRoute $route,
        string $class,
        RouteSitemapDefaults $defaults = null,
    ): Collection
    {
        if (is_subclass_of($class, Model::class)) {
            /** @var Model $class */
            return $class::query()->get()->map(function (Model $model) use ($route, $defaults): Url {
                $url = Url::make(route($route->getName(), $model));
                if ($model->getAttribute('updated_at')) {
                    $url->lastmod($model->getAttribute('updated_at'));
                }

                if ($defaults && $defaults->index !== null) {
                    $url->index($defaults->index);
                }

                return $url;
            });
        }

        $template = app($class);

        if ($template instanceof TemplateContract) {
            $generated = collect($template->generate($route));

            $urls = $generated->map(fn ($item): Url => $item instanceof Url
                ? $item
                : Url::make((string) $item));

            if ($defaults && $defaults->index !== null) {
                $urls = $urls->each(fn (Url $url) => $url->index($defaults->index));
            }

            return $urls;
        }

        if ($template instanceof SitemapProviderInterface) {
            $urls = collect($template->getUrls());

            if ($defaults && $defaults->index !== null) {
                $urls = $urls->each(fn (Url $url) => $url->index($defaults->index));
            }

            return $urls;
        }

        return collect();
    }
}
