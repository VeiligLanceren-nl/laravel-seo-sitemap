<?php

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;

it('prompts user to complete Google Search Console setup', function () {
    File::shouldReceive('exists')->andReturn(false);

    $input = new ArrayInput([]);
    $output = new BufferedOutput();
    $io = new SymfonyStyle($input, $output);

    GooglePingService::setup($io);

    $display = $output->fetch();
    expect($display)->toContain('Google Search Console Setup');
});
