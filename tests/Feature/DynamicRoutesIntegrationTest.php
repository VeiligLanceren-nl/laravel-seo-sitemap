<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRouteChild;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\StaticDynamicRoute;

beforeEach(function () {
    Route::get('/dynamic/{slug}', fn () => 'ok')
        ->name('dynamic.test')
        ->dynamic(fn () => new StaticDynamicRoute([
            DynamicRouteChild::make(['slug' => 'first']),
            DynamicRouteChild::make(['slug' => 'second']),
        ]));
});

it('resolves dynamic route URLs via RouteSitemap::urls()', function () {
    $urls = RouteSitemap::urls();

    $resolvedLocs = $urls->map(fn ($url) => $url->toArray()['loc'])->all();

    expect($resolvedLocs)->toContain(
        url('/dynamic/first'),
        url('/dynamic/second')
    );
});
