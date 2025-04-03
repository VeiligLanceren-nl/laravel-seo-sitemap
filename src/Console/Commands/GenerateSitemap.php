<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Console\Command;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

class GenerateSitemap extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sitemap:generate {--path=} {--disk=} {--pretty}';

    /**
     * @var string
     */
    protected $description = 'Generate a sitemap from routes and save to disk';

    /**
     * @return void
     */
    public function handle(): void
    {
        $path = $this->option('path') ?? config('sitemap.file.path', 'sitemap.xml');
        $disk = $this->option('disk') ?? config('sitemap.file.disk', 'public');
        $pretty = $this->option('pretty') || config('sitemap.pretty', false);

        $sitemap = Sitemap::fromRoutes();

        if ($pretty) {
            $sitemap->options(['pretty' => true]);
        }

        $sitemap->save($path, $disk);

        $this->info("Sitemap saved to [{$disk}] at: {$path}");
    }
}
