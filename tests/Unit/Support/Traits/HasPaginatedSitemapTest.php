<?php

use Illuminate\Routing\Route;
use Tests\Support\Sitemap\ItemTemplate\DummySitemapTemplate;

beforeEach(function () {
    app('router')
        ->get('/dummy', function () {})
        ->name('test.route');
});

function getRouteMock(): Route {
    $route = Mockery::mock(Route::class)->makePartial();
    $route->shouldReceive('getName')->andReturn('test.route');
    return $route;
}

it('generates paginated urls', function () {
    $template = new DummySitemapTemplate();
    $route = getRouteMock();

    $urls = $template->getUrls($route, 5, 2);

    expect($urls)->toHaveCount(3)
        ->and($urls[0]->getLoc())->toBe(url('/dummy?page=1'))
        ->and($urls[1]->getLoc())->toBe(url('/dummy?page=2'))
        ->and($urls[2]->getLoc())->toBe(url('/dummy?page=3'));
});

it('can skip page one', function () {
    $template = new DummySitemapTemplate();
    $route = getRouteMock();

    $urls = $template->getUrls($route, 5, 2, [], true);

    expect($urls)->toHaveCount(2)
        ->and($urls[0]->getLoc())->toBe(url('/dummy?page=2'))
        ->and($urls[1]->getLoc())->toBe(url('/dummy?page=3'));
});

it('merges additional params', function () {
    $template = new DummySitemapTemplate();
    $route = getRouteMock();

    $urls = $template->getUrls($route, 2, 1, ['foo' => 'bar']);

    expect($urls[0]->getLoc())->toBe(url('/dummy?foo=bar&page=1'));
});