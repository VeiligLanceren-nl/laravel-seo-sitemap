<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services\Ping;

use Illuminate\Support\Facades\Http;
use VeiligLanceren\LaravelSeoSitemap\Contracts\PingService;

class BingPingService implements PingService
{
    /**
     * @var string|null
     */
    protected ?string $apiKey;

    /**
     * @var string|null
     */
    protected ?string $host;

    /**
     * @var string|null
     */
    protected ?string $keyLocation;

    public function __construct()
    {
        $this->apiKey = config('sitemap.indexnow.key');
        $this->host = parse_url(config('app.url'), PHP_URL_HOST);
        $this->keyLocation = config('sitemap.indexnow.key_location');
    }

    /**
     * Submit a single URL to IndexNow.
     *
     * @param string $sitemapUrl
     * @return bool
     */
    public function ping(string $sitemapUrl): bool
    {
        $endpoint = 'https://api.indexnow.org/indexnow';
        $payload = [
            'host' => $this->host,
            'key' => $this->apiKey,
            'keyLocation' => $this->keyLocation,
            'urlList' => [$sitemapUrl],
        ];

        $response = Http::post($endpoint, $payload);

        return $response->successful();
    }
}
