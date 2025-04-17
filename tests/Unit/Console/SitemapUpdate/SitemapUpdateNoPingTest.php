<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\Services\SearchEnginePingServiceInterface;

it('does not ping search engines when --no-ping is set', function () {
    $mock = Mockery::mock(SearchEnginePingServiceInterface::class);
    $mock->shouldNotReceive('pingAll');
    $this->app->instance(SearchEnginePingServiceInterface::class, $mock);

    Storage::disk('public')->put('sitemaps/noping.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/about</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta><route>about.route</route></meta>
    </url>
</urlset>
XML));

    Artisan::call('sitemap:update', ['routeName' => 'about.route', '--no-ping' => true]);
    expect(true)->toBeTrue();
});