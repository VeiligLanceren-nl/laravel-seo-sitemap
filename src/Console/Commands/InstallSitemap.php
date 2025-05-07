<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:install {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the sitemap route file and include it in routes/web.php';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $source      = dirname(__DIR__, 3) . '/routes/sitemap.php';
        $destination = base_path('routes/sitemap.php');

        // Publish the sitemap route file
        if (File::exists($destination) && ! $this->option('force')) {
            if (! $this->confirm('routes/sitemap.php already exists. Overwrite?', false)) {
                $this->info('Installation cancelled.');
                return Command::SUCCESS;
            }
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
        $this->info('Published routes/sitemap.php');

        // Add include to routes/web.php
        $webPath     = base_path('routes/web.php');
        $includeLine = "require __DIR__.'/sitemap.php';";

        if (File::exists($webPath)) {
            $contents = File::get($webPath);

            if (! Str::contains($contents, $includeLine)) {
                File::append($webPath, PHP_EOL . $includeLine . PHP_EOL);
                $this->info('Added sitemap include to routes/web.php');
            } else {
                $this->info('routes/web.php already contains sitemap include.');
            }
        } else {
            $this->warn('routes/web.php not found; skipping include.');
        }

        return Command::SUCCESS;
    }
}