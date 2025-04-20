<?php

namespace VeiligLanceren\LaravelSeoSitemap\Contracts;

interface PingService
{
    /**
     * Ping the search engine with the given sitemap URL.
     *
     * @param string $sitemapUrl
     * @return bool
     */
    public function ping(string $sitemapSitemapUrl): bool;
}