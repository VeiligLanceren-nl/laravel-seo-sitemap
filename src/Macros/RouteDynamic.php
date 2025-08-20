<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Closure;
use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRoute;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\InvalidDynamicRouteCallbackException;

class RouteDynamic
{
    /**
     * @return void
     */
    public static function register(): void
    {
        Route::macro('dynamic', function (Closure $callback): Route {
            /** @var Route $this */

            // Optional type check during registration
            $result = $callback();
            if (
                !($result instanceof DynamicRoute) &&
                !(is_iterable($result))
            ) {
                throw new InvalidDynamicRouteCallbackException();
            }

            $this->defaults['sitemap.dynamic'] = $callback;
            return $this;
        });
    }
}
