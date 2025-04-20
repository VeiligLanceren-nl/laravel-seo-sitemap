<?php

namespace VeiligLanceren\LaravelSeoSitemap\Console\Commands;

use Google\Exception;
use Random\RandomException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\IndexNowPingService;

class InstallSitemapCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sitemap:install';

    /**
     * @var string
     */
    protected $description = 'Install and configure Sitemap ping services like Google and IndexNow';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     * @throws RandomException
     */
    public function handle(): int
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $io->title('Sitemap Install Wizard');

        if ($io->confirm('Would you like to configure Google Search Console?')) {
            GooglePingService::setup($io);
        }

        if ($io->confirm('Would you like to configure IndexNow support?')) {
            IndexNowPingService::setup($io);
        }

        $io->success('Sitemap installation and configuration complete.');
        return self::SUCCESS;
    }
}
