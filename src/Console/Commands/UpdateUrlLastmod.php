<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;

class UpdateUrlLastmod extends Command
{
    /**
     * @var string
     */
    protected $signature = 'url:update {routeName}';

    /**
     * @var string
     */
    protected $description = 'Update the lastmod date for a given route name';

    /**
     * @return void
     */
    public function handle(): void
    {
        $routeName = $this->argument('routeName');

        UrlMetadata::updateOrCreate(
            ['route_name' => $routeName],
            ['lastmod' => Carbon::now()]
        );

        $this->info("Updated lastmod for route: {$routeName}");
    }
}