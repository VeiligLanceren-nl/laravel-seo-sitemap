<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteImage;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

beforeEach(function () {
    RouteImage::register();
    RouteSitemap::register();

    Route::get('/test-image', fn () => 'ok')
        ->name('test.image')
        ->image('https://example.com/hero.jpg', 'Hero');
});

it('stores image instances on the route defaults', function () {
    $route = Route::get('/default-image', fn () => 'ok')
        ->name('default.image')
        ->image('https://example.com/cover.jpg', 'Cover');

    $defaults = $route->defaults['sitemap'];

    expect($defaults)->toBeInstanceOf(RouteSitemapDefaults::class)
        ->and($defaults->images)->toHaveCount(1)
        ->and($defaults->images[0])->toBeInstanceOf(Image::class)
        ->and($defaults->images[0]->toArray())->toBe([
            'loc' => 'https://example.com/cover.jpg',
            'title' => 'Cover',
        ]);
});

it('propagates images to generated Url objects', function () {
    $urls = RouteSitemap::urls();

    expect($urls)->toHaveCount(1)
        ->and($urls->first())->toBeInstanceOf(Url::class)
        ->and($urls->first()->getImages())->toHaveCount(1)
        ->and($urls->first()->getImages()[0]->toArray())->toBe([
            'loc' => 'https://example.com/hero.jpg',
            'title' => 'Hero',
        ]);
});
