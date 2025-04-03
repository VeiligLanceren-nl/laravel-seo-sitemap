<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

it('includes only GET routes with sitemap default', function () {
    Route::get('/included', fn () => 'included')
        ->name('included')
        ->sitemap()
        ->priority('0.8');

    Route::post('/excluded', fn () => 'excluded')->name('excluded')->sitemap();

    $sitemap = Sitemap::fromRoutes();
    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(1);
    expect($items[0]['loc'])->toBe(URL::to('/included'));
    expect($items[0]['priority'])->toBe('0.8');
});
