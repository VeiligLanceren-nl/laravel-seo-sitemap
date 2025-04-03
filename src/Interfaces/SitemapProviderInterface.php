<?php

namespace VeiligLanceren\LaravelSeoSitemap\Interfaces;

use Illuminate\Support\Collection;

interface SitemapProviderInterface
{
    /**
     * @return Collection<\VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url>
     */
    public function getUrls(): Collection;
}