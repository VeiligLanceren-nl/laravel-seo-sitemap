<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services;

use VeiligLanceren\LaravelSeoSitemap\Contracts\PingService;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\Services\SearchEnginePingServiceInterface;

class SearchEnginePingService implements SearchEnginePingServiceInterface
{
    /**
     * @param array<PingService> $services
     */
    public function __construct(protected array $services) {}

    /**
     * {@inheritDoc}
     */
    public function pingAll(string $sitemapUrl): array
    {
        $results = [];

        foreach ($this->services as $service) {
            $key = class_basename($service);
            $results[$key] = $service->ping($sitemapUrl);
        }

        return $results;
    }
}