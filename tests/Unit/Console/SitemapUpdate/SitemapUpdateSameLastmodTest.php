<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;

uses()->beforeEach(function () {
    $mock = Mockery::mock(GooglePingService::class);
    $mock->shouldReceive('ping')->andReturnTrue();

    App::instance(GooglePingService::class, $mock);
});

it('does not update lastmod if timestamp is unchanged', function () {
    $path = base_path('tests/Fixtures/unchanged-template.blade.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'Hi');
    touch($path, strtotime('2023-01-01 00:00:00'));

    Storage::disk('public')->put('sitemaps/unchanged.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/unchanged</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta><route>unchanged.route</route><source>tests/Fixtures/unchanged-template.blade.php</source></meta>
    </url>
</urlset>
XML));

    Artisan::call('sitemap:update', ['routeName' => 'unchanged.route', '--no-ping' => true]);
    $metadata = UrlMetadata::where('route_name', 'unchanged.route')->first();
    expect($metadata)->toBeNull();
});