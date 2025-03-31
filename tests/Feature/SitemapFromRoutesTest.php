<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap;

beforeEach(function () {
    Route::middleware([])->group(function () {
        Route::get('/included', fn () => 'Included')
            ->name('included')
            ->defaults('sitemap', true)
            ->defaults('sitemap_priority', '0.8');

        Route::get('/excluded', fn () => 'Excluded')
            ->name('excluded')
            ->defaults('sitemap', false);

        Route::post('/post-only', fn () => 'Post')
            ->name('post.only')
            ->defaults('sitemap', true);
    });
});

it('includes only GET routes with sitemap default', function () {
    $sitemap = Sitemap::fromRoutes();

    $urls = $sitemap->toArray()['urls'];

    expect($urls)->toHaveCount(1);
    expect($urls[0]['loc'])->toBe(url('/included'));
    expect($urls[0]['priority'])->toBe('0.8');
});