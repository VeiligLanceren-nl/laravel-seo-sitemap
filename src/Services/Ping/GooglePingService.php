<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services\Ping;

use Google\Client;
use Google\Exception;
use Google\Service\SearchConsole;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use VeiligLanceren\LaravelSeoSitemap\Contracts\PingService;

class GooglePingService implements PingService
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var SearchConsole
     */
    protected SearchConsole $searchConsole;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $credentialsPath = storage_path('app/google/credentials.json');
        $tokenPath = storage_path('app/google/token.json');

        $this->client = new Client();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->addScope(SearchConsole::WEBMASTERS);
        $this->client->setAccessType('offline');

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $this->authorizeInteractively($tokenPath);
            }

            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }

            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }

        $this->searchConsole = new SearchConsole($this->client);
    }

    /**
     * @param string $sitemapUrl
     * @return bool
     */
    public function ping(string $sitemapUrl): bool
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

    /**
     * Ask user for authorization code
     *
     * @param string $tokenPath
     * @return void
     */
    protected function authorizeInteractively(string $tokenPath): void
    {
        $authUrl = $this->client->createAuthUrl();
        echo "Open the following link in your browser:\n$authUrl\n";
        echo 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        $this->client->setAccessToken($accessToken);

        if (array_key_exists('error', $accessToken)) {
            throw new \Exception(join(', ', $accessToken));
        }

        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }

        file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
    }

    /**
     * Setup helper to run during artisan command.
     *
     * @param SymfonyStyle $io
     * @return void
     * @throws Exception
     */
    public static function setup(SymfonyStyle $io): void
    {
        $io->title('Google Search Console Setup');

        $credentialsPath = storage_path('app/google/credentials.json');
        if (!File::exists($credentialsPath)) {
            $io->warning('Google credentials not found. Please upload your OAuth credentials JSON to: ' . $credentialsPath);
            return;
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(SearchConsole::WEBMASTERS);
        $client->setAccessType('offline');

        $authUrl = $client->createAuthUrl();
        $io->writeln("Open the following link in your browser:\n$authUrl");
        $authCode = $io->ask('Enter verification code');

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        if (array_key_exists('error', $accessToken)) {
            $io->error('Authorization failed: ' . join(', ', $accessToken));
            return;
        }

        File::ensureDirectoryExists(dirname(storage_path('app/google/token.json')));
        File::put(storage_path('app/google/token.json'), json_encode($accessToken));

        $io->success('Google Search Console setup complete.');
    }
}
