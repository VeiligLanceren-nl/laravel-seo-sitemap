<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url as SitemapUrl;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

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

        $urls = RouteSitemap::urls();

        $hasIndex = $urls->contains(fn (SitemapUrl $url) => $url->getIndex() !== null);

        if (! $hasIndex) {
            $sitemap = Sitemap::make($urls->all());

            if ($pretty) {
                $sitemap->options(['pretty' => true]);
            }

            $sitemap->save($path, $disk);

            $this->info("Sitemap saved to [{$disk}] at: {$path}");

            return;
        }

        $groups = $urls->groupBy(fn (SitemapUrl $url) => $url->getIndex() ?? 'default');

        $baseName = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'xml';
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $directory = $directory === '.' ? '' : $directory . '/';

        $index = SitemapIndex::make(null, null, ['pretty' => $pretty]);

        foreach ($groups as $name => $groupUrls) {
            $fileName = sprintf('%s%s-%s.%s', $directory, $baseName, $name, $extension);
            $sitemap = Sitemap::make($groupUrls->all());

            if ($pretty) {
                $sitemap->options(['pretty' => true]);
            }

            $sitemap->save($fileName, $disk);

            $index->add(URL::to('/' . $fileName));
        }

        Storage::disk($disk)->put($path, $index->toXml());

        $this->info("Sitemap index saved to [{$disk}] at: {$path}");
    }
}
