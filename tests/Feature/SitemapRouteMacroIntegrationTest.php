<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteChangefreq;
use VeiligLanceren\LaravelSeoSitemap\Macros\RoutePriority;

beforeEach(function () {
    RouteSitemap::register();
    RouteChangefreq::register();
    RoutePriority::register();
});

it('includes sitemap macro route in generated urls', function () {
    Route::get('/macro-sitemap', fn () => 'ok')
        ->sitemap();

    $sitemap = Sitemap::fromRoutes();
    $urls = $sitemap->toArray()['urls'];

    expect($urls)->toHaveCount(1);
    expect($urls[0]['loc'])->toBe('http://localhost/macro-sitemap');
});

it('includes changefreq macro in sitemap url', function () {
    Route::get('/macro-changefreq', fn () => 'ok')
        ->sitemap()
        ->changefreq('weekly');

    $sitemap = Sitemap::fromRoutes();
    $xml = $sitemap->toXml();

    expect($xml)->toContain('<changefreq>weekly</changefreq>');
});

it('includes priority macro in sitemap url', function () {
    Route::get('/macro-priority', fn () => 'ok')
        ->sitemap()
        ->priority('0.9');

    $sitemap = Sitemap::fromRoutes();
    $array = $sitemap->toArray();

    expect($array['urls'][0])
        ->toHaveKey('priority', 0.9);
});