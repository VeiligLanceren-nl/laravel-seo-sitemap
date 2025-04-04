<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;

beforeEach(function () {
    RouteSitemap::register();
});

it('adds the sitemap default to the route', function () {
    $route = Route::get('/test', fn () => 'ok')
        ->name('test')
        ->sitemap();

    expect($route->defaults['sitemap'])->toBeInstanceOf(RouteSitemapDefaults::class);
    expect($route->defaults['sitemap']->enabled)->toBeTrue();
});

it('returns the route instance for chaining', function () {
    $route = new RoutingRoute(['GET'], '/chained', fn () => 'ok');
    $result = $route->sitemap();

    expect($result)->toBeInstanceOf(RoutingRoute::class);
});