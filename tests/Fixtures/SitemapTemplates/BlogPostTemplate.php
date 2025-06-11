<?php

namespace Tests\Fixtures\SitemapTemplates;

use Illuminate\Support\Collection;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;

/**
 * @implements SitemapProviderInterface
 */
class BlogPostTemplate implements SitemapProviderInterface
{
    /**
     * @return Collection<int, Url>
     */
    public function getUrls(): Collection
    {
        return Collection::make([
            Url::make('http://localhost/blog/ai'),
            Url::make('http://localhost/blog/ai/how-to-use-laravel'),
        ]);
    }
}