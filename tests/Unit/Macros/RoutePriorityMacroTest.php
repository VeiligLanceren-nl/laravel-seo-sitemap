<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RoutePriority;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

beforeEach(function () {
    RoutePriority::register();

    Route::get('/test-priority', fn () => 'ok')
        ->name('test.priority')
        ->priority('0.8');
});

it('adds priority to route defaults', function () {
    $route = Route::get('/test-priority', fn () => 'ok')
        ->name('test-priority')
        ->priority(0.8);

    expect($route)->not->toBeNull();
    expect($route->defaults['sitemap'])->toBeInstanceOf(RouteSitemapDefaults::class);
    expect($route->defaults['sitemap']->priority)->toBe("0.8");
});