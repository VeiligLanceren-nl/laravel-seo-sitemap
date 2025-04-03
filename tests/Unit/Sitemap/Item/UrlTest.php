<?php

use Carbon\Carbon;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

it('can be created using the make factory method with all parameters', function () {
    $url = Url::make('/test', '2024-01-01', '0.8', ChangeFrequency::DAILY);

    expect($url->toArray())->toMatchArray([
        'loc' => '/test',
        'lastmod' => '2024-01-01',
        'priority' => '0.8',
        'changefreq' => 'daily',
    ]);
});

it('can be created using the make factory method with DateTimeInterface', function () {
    $url = Url::make('/test', now(), '1.0', ChangeFrequency::WEEKLY);

    expect($url->toArray())->toMatchArray([
        'loc' => '/test',
        'lastmod' => now()->format('Y-m-d'),
        'priority' => '1.0',
        'changefreq' => 'weekly',
    ]);
});


it('sets and returns all fields fluently', function () {
    $url = (new Url())
        ->loc('/foo')
        ->lastmod('2024-01-01')
        ->priority('0.5')
        ->changefreq(ChangeFrequency::WEEKLY);

    expect($url->toArray())->toMatchArray([
        'loc' => '/foo',
        'lastmod' => '2024-01-01',
        'priority' => '0.5',
        'changefreq' => 'weekly',
    ]);
});

it('formats DateTimeInterface for lastmod', function () {
    $date = Carbon::create(2024, 12, 25);
    $url = (new Url())->loc('/xmas')->lastmod($date);

    expect($url->toArray())->toMatchArray([
        'loc' => '/xmas',
        'lastmod' => '2024-12-25',
    ]);
});

it('filters out null values in toArray', function () {
    $url = (new Url())->loc('/only-loc');

    expect($url->toArray())->toBe([
        'loc' => '/only-loc',
    ]);
});
