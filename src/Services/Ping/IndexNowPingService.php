<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services\Ping;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Random\RandomException;
use Symfony\Component\Console\Style\SymfonyStyle;
use VeiligLanceren\LaravelSeoSitemap\Contracts\PingService;

class IndexNowPingService implements PingService
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

    /**
     * Setup helper to run during artisan command.
     *
     * @param SymfonyStyle $io
     * @return void
     * @throws RandomException
     */
    public static function setup(SymfonyStyle $io): void
    {
        $io->title('IndexNow Setup');

        $defaultKey = bin2hex(random_bytes(16));
        $key = $io->ask('Enter your IndexNow key', $defaultKey);

        $filename = $key . '.txt';
        $publicPath = public_path($filename);

        File::put($publicPath, $key);
        $url = url($filename);

        $io->info("Key file written to: {$publicPath}");
        $io->info("Key location URL: {$url}");

        $envUpdates = [
            'SITEMAP_INDEXNOW_KEY' => $key,
            'SITEMAP_INDEXNOW_KEY_LOCATION' => $url,
        ];

        foreach ($envUpdates as $key => $value) {
            static::writeToEnv($key, $value);
        }

        $io->success('IndexNow setup complete. Add the key and location to your config if not using .env.');
    }

    /**
     * Write environment variable to .env file.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    protected static function writeToEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $contents = File::get($envPath);

        if (str_contains($contents, "{$key}=")) {
            $contents = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $contents);
        } else {
            $contents .= PHP_EOL . "{$key}={$value}";
        }

        File::put($envPath, $contents);
    }
}
