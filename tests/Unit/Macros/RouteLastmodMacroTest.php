<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteLastmod;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

beforeEach(function () {
    RouteLastmod::register();

    Route::get('/test-lastmod', fn () => 'ok')
        ->name('test.lastmod')
        ->lastmod('2024-05-01');
});

it('adds lastmod to route defaults', function () {
    $route = Route::get('/another-lastmod', fn () => 'ok')
        ->name('another-lastmod')
        ->lastmod('2024-05-01');

    expect($route)->not->toBeNull();
    expect($route->defaults['sitemap'])->toBeInstanceOf(RouteSitemapDefaults::class);
    expect($route->defaults['sitemap']->lastmod)->toBe('2024-05-01');
});
