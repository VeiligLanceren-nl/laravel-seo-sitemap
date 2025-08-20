<?php

namespace VeiligLanceren\LaravelSeoSitemap\Macros;

use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

class RouteImage
{
    /**
     * @return void
     */
    public static function register(): void
    {
        RoutingRoute::macro('image', function (string $url, ?string $title = null) {
            /** @var RoutingRoute $this */
            $existing = $this->defaults['sitemap'] ?? new RouteSitemapDefaults();

            $existing->enabled = true;

            $image = Image::make($url);

            if ($title !== null) {
                $image->title($title);
            }

            $existing->images[] = $image;

            $this->defaults['sitemap'] = $existing;

            return $this;
        });
    }
}
