<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Tester\CommandTester;
use VeiligLanceren\LaravelSeoSitemap\Console\InstallSitemapCommand;

it('can run the sitemap:install command', function () {
    $exitCode = Artisan::call('sitemap:install', [
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0);
});

it('shows title and options', function () {
    $command = new InstallSitemapCommand();
    $tester = new CommandTester($command);

    $tester->execute([], ['interactive' => false]);

    $display = $tester->getDisplay();
    expect($display)->toContain('Sitemap Install Wizard');
    expect($display)->toContain('Google Search Console');
    expect($display)->toContain('IndexNow');
});
