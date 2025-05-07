<?php

use Tests\Support\Models\DummyModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as LaravelRoute;
use Tests\Support\Sitemap\ItemTemplate\DummyTemplate;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemapUsing;

beforeEach(function () {
    RouteSitemapUsing::register();
    RouteSitemap::register();
});

it('adds the sitemap_generator default to the route when using a template class', function () {
    $route = Route::get('/template/{slug}', fn () => 'ok')
        ->name('test.template')
        ->sitemapUsing(DummyTemplate::class);

    expect($route->defaults)->toHaveKey('sitemap_generator')
        ->and($route->defaults['sitemap_generator'])->toBe(DummyTemplate::class)
        ->and($route->defaults['sitemap'])->toBeTrue();
});

it('adds the sitemap_generator default to the route when using a model class', function () {
    $route = Route::get('/model/{id}', fn () => 'ok')
        ->name('test.model')
        ->sitemapUsing(DummyModel::class);

    expect($route->defaults)->toHaveKey('sitemap_generator')
        ->and($route->defaults['sitemap_generator'])->toBe(DummyModel::class)
        ->and($route->defaults['sitemap'])->toBeTrue();
});

it('returns the route instance for chaining', function () {
    $route  = new LaravelRoute(['GET'], '/chained/{x}', fn () => 'ok');
    $result = $route->sitemapUsing(DummyTemplate::class);

    expect($result)->toBeInstanceOf(LaravelRoute::class);
});

it('RouteSitemap::urls() returns Url instances from the template', function () {
    Route::get('/list/{slug}', fn () => 'ok')
        ->name('test.list')
        ->sitemapUsing(DummyTemplate::class);

    $urls = RouteSitemap::urls();

    expect($urls)->toHaveCount(2)
        ->and($urls->first())->toBeInstanceOf(Url::class)
        ->and($urls->first()->toArray()['loc'])->toBe('https://example.com/first');
});