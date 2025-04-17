<?php

namespace VeiligLanceren\LaravelSeoSitemap\Interfaces\Services;

interface SearchEnginePingServiceInterface
{
    /**
     * Ping all registered services.
     *
     * @param string $sitemapUrl
     * @return array
     */
    public function pingAll(string $sitemapUrl): array;
}