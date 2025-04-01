<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteChangefreq;

beforeEach(function () {
    RouteChangefreq::register();

    Route::middleware('web')->group(function () {
        Route::get('/test-changefreq', fn () => 'ok')
            ->name('test.changefreq')
            ->changefreq('daily');
    });
});

it('adds changefreq to the route definition', function () {
    $route = collect(Route::getRoutes()->getIterator())
        ->first(fn ($r) => $r->uri === 'test-changefreq');

    expect($route)->not->toBeNull()
        ->and($route->defaults)->toHaveKey('sitemap_changefreq')
        ->and($route->defaults['sitemap_changefreq'])->toBe('daily');
});