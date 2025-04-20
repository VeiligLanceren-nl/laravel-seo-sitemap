<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItem;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\Services\SearchEnginePingServiceInterface;

class UpdateUrlLastmodCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sitemap:update {routeName : Route name of the URL that should be updated} {--no-ping : Do not ping search engines after update}';

    /**
     * @var string
     */
    protected $description = 'Update the lastmod attribute for a given URL and optionally ping search engines';

    /**
     * @param SearchEnginePingServiceInterface $pinger
     * @return int
     */
    public function handle(SearchEnginePingServiceInterface $pinger): int
    {
        $sitemaps = Sitemap::load();
        $routeName = $this->argument('routeName');

        foreach ($sitemaps as $sitemap) {
            $hasChanges = false;

            foreach ($sitemap->items as $item) {
                if (($item->meta['route'] ?? null) !== $routeName) {
                    continue;
                }

                $originalLastmod = $item->lastmod;
                $newLastmod = $this->detectLastModificationTime($item);

                if ($newLastmod && $newLastmod !== $originalLastmod) {
                    $item->lastmod = $newLastmod;
                    $hasChanges = true;

                    if (isset($item->meta['route'])) {
                        UrlMetadata::query()
                            ->updateOrCreate(
                                ['route_name' => $item->meta['route']],
                                ['lastmod' => $item->lastmod]
                            );
                    }
                }
            }

            if ($hasChanges) {
                $disk = config('sitemap.disk', 'public');
                $path = config('sitemap.path', 'sitemap.xml');

                $sitemap->save($path, $disk);
            }
        }

        if (! $this->option('no-ping')) {
            $this->info('Pinging search engines...');
            $pinger->pingAll(config('sitemap.url', url('/sitemap.xml')));
        } else {
            $this->info('Search engine ping skipped.');
        }

        $this->info('Lastmod attributes updated successfully.');
        return self::SUCCESS;
    }

    /**
     * Detect last modification time for a URL based on route/controller/template/etc.
     *
     * @param SitemapItem $item
     * @return string|null
     */
    protected function detectLastModificationTime(SitemapItem $item): ?string
    {
        if (! isset($item->meta['source'])) {
            return null;
        }

        $sourcePath = base_path($item->meta['source']);

        if (File::exists($sourcePath)) {
            return now()->parse(File::lastModified($sourcePath))->toAtomString();
        }

        return null;
    }
}