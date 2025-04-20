<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use VeiligLanceren\Sitemap\Dynamic\DynamicRouteChild;
use VeiligLanceren\Sitemap\Dynamic\StaticDynamicRoute;
use function Pest\Laravel\get;

beforeEach(function () {
    Route::get('/test/{slug}', fn () => 'ok')
        ->name('test.dynamic')
        ->dynamic(fn () => new StaticDynamicRoute([
            DynamicRouteChild::make(['slug' => 'one']),
            DynamicRouteChild::make(['slug' => 'two']),
        ]));

    Route::get('/fallback/{slug}', fn () => 'ok')
        ->name('test.fallback')
        ->dynamic(fn () => [
            ['slug' => 'a'],
            ['slug' => 'b'],
        ]);
});

it('registers dynamic macro and stores closure under defaults', function () {
    $route = Route::getRoutes()->getByName('test.dynamic');

    expect($route->defaults)
        ->toHaveKey('sitemap.dynamic')
        ->and($route->defaults['sitemap.dynamic'])->toBeInstanceOf(Closure::class);
});

it('returns correct parameters from StaticDynamicRoute', function () {
    $route = Route::getRoutes()->getByName('test.dynamic');
    $provider = $route->defaults['sitemap.dynamic'];
    $result = $provider();

    expect($result)->toBeInstanceOf(StaticDynamicRoute::class)
        ->and($result->parameters())->toHaveCount(2)
        ->and($result->parameters()->pluck('slug'))->toEqual(collect(['one', 'two']));
});

it('supports raw array return and generates parameter sets', function () {
    $route = Route::getRoutes()->getByName('test.fallback');
    $provider = $route->defaults['sitemap.dynamic'];
    $result = $provider();

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBe(['slug' => 'a']);
});
