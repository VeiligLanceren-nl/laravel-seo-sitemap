<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRouteChild;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\StaticDynamicRoute;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\InvalidDynamicRouteCallbackException;

beforeEach(function () {
    test()->testDynamicRoute = Route::get('/test/{slug}', fn () => 'ok')
        ->name('test.dynamic')
        ->dynamic(fn () => new StaticDynamicRoute([
            DynamicRouteChild::make(['slug' => 'one']),
            DynamicRouteChild::make(['slug' => 'two']),
        ]));

    test()->testFallbackRoute = Route::get('/fallback/{slug}', fn () => 'ok')
        ->name('test.fallback')
        ->dynamic(fn () => [
            ['slug' => 'a'],
            ['slug' => 'b'],
        ]);
});

it('registers dynamic macro and stores closure under defaults', function () {
    $route = test()->testDynamicRoute;

    expect($route)->not->toBeNull()
        ->and($route->defaults)
        ->toHaveKey('sitemap.dynamic')
        ->and($route->defaults['sitemap.dynamic'])->toBeInstanceOf(Closure::class);
});

it('returns correct parameters from StaticDynamicRoute', function () {
    $route = test()->testDynamicRoute;
    $provider = $route->defaults['sitemap.dynamic'];
    $result = $provider();

    expect($result)->toBeInstanceOf(StaticDynamicRoute::class)
        ->and($result->parameters())->toHaveCount(2)
        ->and($result->parameters()->pluck('slug'))->toEqual(collect(['one', 'two']));
});

it('supports raw array return and generates parameter sets', function () {
    $route = test()->testFallbackRoute;
    $provider = $route->defaults['sitemap.dynamic'];
    $result = $provider();

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBe(['slug' => 'a']);
});

it('throws a custom exception when callback returns invalid type', function () {
    expect(fn () => Route::get('/bad/{slug}', fn () => 'ok')
        ->name('bad.route')
        ->dynamic(fn () => 123))
        ->toThrow(InvalidDynamicRouteCallbackException::class);
});
