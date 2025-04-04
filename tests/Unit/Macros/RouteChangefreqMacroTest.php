<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteChangefreq;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

beforeEach(function () {
    RouteChangefreq::register();

    Route::middleware('web')->group(function () {
        Route::get('/test-changefreq', fn () => 'ok')
            ->name('test.changefreq')
            ->changefreq('daily');
    });
});

it('adds changefreq to the route definition', function () {
    $route = Route::get('/test-changefreq', fn () => 'ok')
        ->name('test-changefreq')
        ->changefreq('daily');

    expect($route)->not->toBeNull();
    expect($route->defaults['sitemap'])->toBeInstanceOf(RouteSitemapDefaults::class);
    expect($route->defaults['sitemap']->changefreq)->toBe(ChangeFrequency::DAILY);
});