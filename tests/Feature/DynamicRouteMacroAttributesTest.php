<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRouteChild;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\StaticDynamicRoute;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

beforeEach(function () {
    Route::get('/dynamic/{slug}', fn () => 'ok')
        ->name('dynamic.test')
        ->sitemap()
        ->changefreq(ChangeFrequency::DAILY)
        ->priority('0.8')
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

it('applies changefreq and priority macros to dynamic URLs', function () {
    $urls = RouteSitemap::urls();

    foreach ($urls as $url) {
        $array = $url->toArray();
        expect($array['changefreq'] ?? null)->toBe('daily');
        expect($array['priority'] ?? null)->toBe('0.8');
    }
});
