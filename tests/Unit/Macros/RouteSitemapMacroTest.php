<?php

use Illuminate\Routing\Route as RoutingRoute;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;

beforeEach(function () {
    RouteSitemap::register();
});

it('adds the sitemap default to the route', function () {
    $route = new RoutingRoute(['GET'], '/test', fn () => 'ok');
    $route->sitemap();

    expect($route->defaults)->toHaveKey('sitemap');
    expect($route->defaults['sitemap'])->toBeTrue();
});

it('returns the route instance for chaining', function () {
    $route = new RoutingRoute(['GET'], '/chained', fn () => 'ok');
    $result = $route->sitemap();

    expect($result)->toBeInstanceOf(RoutingRoute::class);
});