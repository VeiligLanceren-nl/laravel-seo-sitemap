<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Closure;
use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRoute;

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
                throw new \InvalidArgumentException(
                    'The callback for ->dynamic() must return a DynamicRoute or iterable of parameter arrays.'
                );
            }

            $this->defaults['sitemap.dynamic'] = $callback;
            return $this;
        });
    }
}
