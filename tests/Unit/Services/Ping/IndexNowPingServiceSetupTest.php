<?php

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\IndexNowPingService;

it('generates key and updates .env for IndexNow', function () {
    File::shouldReceive('put')->andReturn(true);
    File::shouldReceive('get')->andReturn('');
    File::shouldReceive('exists')->andReturn(false);
    File::shouldReceive('ensureDirectoryExists')->andReturn(true);

    $input = new ArrayInput([]);
    $output = new BufferedOutput();
    $io = new SymfonyStyle($input, $output);

    IndexNowPingService::setup($io);

    $display = $output->fetch();
    expect($display)->toContain('IndexNow Setup');
    expect($display)->toContain('IndexNow setup complete');
});
