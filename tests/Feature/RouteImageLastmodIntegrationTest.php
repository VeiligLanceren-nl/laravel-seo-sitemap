<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

it('adds lastmod and images from route macros to the sitemap', function () {
    Route::get('/media', fn () => 'ok')
        ->sitemap()
        ->lastmod('2024-05-01')
        ->image('https://example.com/hero.jpg', 'Hero');

    $xml = Sitemap::fromRoutes()->toXml();

    expect($xml)
        ->toContain('<loc>' . URL::to('/media') . '</loc>')
        ->and($xml)->toContain('<lastmod>2024-05-01</lastmod>')
        ->and($xml)->toContain('<image:image')
        ->and($xml)->toContain('<image:loc>https://example.com/hero.jpg</image:loc>')
        ->and($xml)->toContain('<image:title>Hero</image:title>');
});
