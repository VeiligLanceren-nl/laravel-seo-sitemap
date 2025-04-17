<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services\Ping;

use Google\Client;
use Google\Service\SearchConsole;
use Illuminate\Support\Facades\Log;
use VeiligLanceren\LaravelSeoSitemap\Contracts\PingService;

class GooglePingService implements PingService
{
    protected $client;
    protected $searchConsole;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->addScope(SearchConsole::WEBMASTERS);
        $this->client->setAccessType('offline');

        if (file_exists(storage_path('app/google/token.json'))) {
            $accessToken = json_decode(file_get_contents(storage_path('app/google/token.json')), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                if (array_key_exists('error', $accessToken)) {
                    throw new \Exception(join(', ', $accessToken));
                }
            }

            if (!file_exists(dirname(storage_path('app/google/token.json')))) {
                mkdir(dirname(storage_path('app/google/token.json')), 0700, true);
            }

            file_put_contents(storage_path('app/google/token.json'), json_encode($this->client->getAccessToken()));
        }

        $this->searchConsole = new SearchConsole($this->client);
    }

    /**
     * @param string $sitemapUrl
     * @return bool
     */
    public function ping(string $sitemapSitemapUrl): bool
    {
        try {
            $siteUrl = $this->extractSiteUrl($sitemapUrl);
            $this->searchConsole->sitemaps->submit($siteUrl, $sitemapUrl);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Search Console submission failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param string $sitemapUrl
     * @return string
     */
    protected function extractSiteUrl(string $sitemapUrl): string
    {
        $parsedUrl = parse_url($sitemapUrl);

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/';
    }
}
