<?php

namespace Tests\Support\Sitemap\ItemTemplate;

use Traversable;
use Illuminate\Routing\Route as LaravelRoute;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItemTemplate;

class DummyTemplate implements SitemapItemTemplate
{
    /**
     * @param LaravelRoute $route
     * @return iterable<Url>
     */
    public function generate(LaravelRoute $route): iterable
    {
        return [
            Url::make('https://example.com/first'),
            Url::make('https://example.com/second'),
        ];
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        yield from $this->generate(app(LaravelRoute::class));
    }
}