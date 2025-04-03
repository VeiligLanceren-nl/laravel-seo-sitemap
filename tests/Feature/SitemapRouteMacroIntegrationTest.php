<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

it('includes sitemap macro route', function () {
    Route::get('/macro-sitemap', fn () => 'ok')
        ->sitemap();

    $sitemap = Sitemap::fromRoutes();
    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(1);
    expect($items[0]['loc'])->toBe(URL::to('/macro-sitemap'));
});

it('includes priority macro in sitemap output', function () {
    Route::get('/priority', fn () => 'ok')
        ->sitemap()
        ->priority(0.9);

    $sitemap = Sitemap::fromRoutes();
    $array = $sitemap->toArray();

    expect($array['items'][0])
        ->toHaveKey('priority', 0.9);
});