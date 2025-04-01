<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Macros\RoutePriority;

beforeEach(function () {
    RoutePriority::register();

    Route::get('/test-priority', fn () => 'ok')
        ->name('test.priority')
        ->priority('0.8');
});

it('adds sitemap_priority to route defaults', function () {
    /** @var RoutingRoute $route */
    $route = collect(Route::getRoutes()->getIterator())
        ->first(fn ($r) => $r->uri === 'test-priority');

    expect($route)->not->toBeNull()
        ->and($route->defaults)->toHaveKey('sitemap_priority')
        ->and($route->defaults['sitemap_priority'])->toBe('0.8');
});
