<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;

uses()->beforeEach(function () {
    $mock = Mockery::mock(GooglePingService::class);
    $mock->shouldReceive('ping')->andReturnTrue();

    App::instance(GooglePingService::class, $mock);
});

it('gracefully skips invalid XML files', function () {
    Storage::disk('public')->put('sitemaps/broken.xml', <<<XML
Oops <notxml>
XML);

    Artisan::call('sitemap:update', ['routeName' => 'broken.route', '--no-ping' => true]);

    expect(true)->toBeTrue();
});