<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;

uses()->beforeEach(function () {
    $mock = Mockery::mock(GooglePingService::class);
    $mock->shouldReceive('ping')->andReturnTrue();

    App::instance(GooglePingService::class, $mock);
});

it('does nothing if the routeName does not exist in any sitemap', function () {
    Storage::disk('public')->put('sitemaps/nomatch.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/about</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta>
            <route>about.route</route>
        </meta>
    </url>
</urlset>
XML));

    Artisan::call('sitemap:update', ['routeName' => 'missing.route', '--no-ping' => true]);
    expect(UrlMetadata::where('route_name', 'missing.route')->exists())->toBeFalse();
});