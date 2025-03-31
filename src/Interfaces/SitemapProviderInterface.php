<?php

namespace VeiligLanceren\LaravelSeoSitemap\Interfaces;

use Illuminate\Support\Collection;

interface SitemapProviderInterface
{
    /**
     * @return Collection<\VeiligLanceren\LaravelSeoSitemap\Url>
     */
    public function getUrls(): Collection;
}