<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use IteratorAggregate;
use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Url;

/**
 * A class that can be attached to a route with `->sitemapUsing()`.
 * It must return one or more {@see \VeiligLanceren\LaravelSeoSitemap\Url}
 * instances (or raw strings that will be wrapped into Url objects) for the
 * given route.
 *
 * @extends IteratorAggregate<int, Url|string>
 */
interface SitemapItemTemplate extends IteratorAggregate
{
    /**
     * @param Route $route The route to which the template is bound.
     * @return iterable<Url|string>
     */
    public function generate(Route $route): iterable;
}