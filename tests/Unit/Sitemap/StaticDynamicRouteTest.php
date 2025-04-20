<?php

use Illuminate\Support\Collection;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRouteChild;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\StaticDynamicRoute;

it('creates a StaticDynamicRoute with children and returns parameter sets', function () {
    $children = [
        DynamicRouteChild::make(['slug' => 'first']),
        DynamicRouteChild::make(['slug' => 'second']),
    ];

    $route = new StaticDynamicRoute($children);
    $parameters = $route->parameters();

    expect($parameters)->toBeInstanceOf(Collection::class)
        ->and($parameters)->toHaveCount(2)
        ->and($parameters->pluck('slug'))->toEqual(collect(['first', 'second']));
});

it('returns an empty collection when given no children', function () {
    $route = new StaticDynamicRoute([]);
    $parameters = $route->parameters();

    expect($parameters)->toBeInstanceOf(Collection::class)
        ->and($parameters)->toBeEmpty();
});
