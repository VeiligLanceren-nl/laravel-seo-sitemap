<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

beforeEach(function () {
    Storage::fake('public');
    App::forgetInstance('dummy-provider');
});

it('creates a sitemap with loc only', function () {
    $sitemap = Sitemap::make([
        \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'items' => [['loc' => 'https://example.com']],
    ]);
});

it('creates a sitemap with loc and lastmod', function () {
    $sitemap = Sitemap::make([
        \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')->lastmod('2024-01-01')
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'items' => [['loc' => 'https://example.com', 'lastmod' => '2024-01-01']],
    ]);
});

it('creates a sitemap with loc, lastmod, and changefreq', function () {
    $sitemap = Sitemap::make([
        \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')
            ->lastmod('2024-01-01')
            ->changefreq(ChangeFrequency::WEEKLY)
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'items' => [[
            'loc' => 'https://example.com',
            'lastmod' => '2024-01-01',
            'changefreq' => 'weekly',
        ]],
    ]);

    $xml = $sitemap->toXml();

    expect($xml)->toContain('<changefreq>weekly</changefreq>');
});

it('creates pretty XML when enabled', function () {
    $sitemap = Sitemap::make([
        \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')->lastmod('2025-01-01')
    ], [
        'pretty' => true
    ]);

    $xml = $sitemap->toXml();

    expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
    expect($xml)->toContain('<urlset');
    expect($xml)->toContain('<loc>https://example.com</loc>');
    expect($xml)->toContain('<lastmod>2025-01-01</lastmod>');
});

it('saves the sitemap to disk', function () {
    $sitemap = Sitemap::make([
        \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')->lastmod('2025-01-01')
    ]);

    $sitemap->save('sitemap.xml', 'public');
    Storage::disk('public')->assertExists('sitemap.xml');

    $content = Storage::disk('public')->get('sitemap.xml');
    expect($content)->toContain('<loc>https://example.com</loc>');
});

it('includes images in the sitemap array and XML output', function () {
    $url = \VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url::make('https://example.com')
        ->addImage(Image::make('https://example.com/image.jpg')
            ->caption('Homepage')
            ->title('Hero image')
            ->license('https://example.com/license')
            ->geoLocation('Netherlands'));

    $sitemap = Sitemap::make([$url]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'items' => [[
            'loc' => 'https://example.com',
            'images' => [[
                'loc' => 'https://example.com/image.jpg',
                'caption' => 'Homepage',
                'title' => 'Hero image',
                'license' => 'https://example.com/license',
                'geo_location' => 'Netherlands',
            ]],
        ]],
    ]);

    $xml = $sitemap->toXml();

    expect($xml)->toContain('<image:image');
    expect($xml)->toContain('<image:loc>https://example.com/image.jpg</image:loc>');
    expect($xml)->toContain('<image:caption>Homepage</image:caption>');
    expect($xml)->toContain('<image:title>Hero image</image:title>');
    expect($xml)->toContain('<image:license>https://example.com/license</image:license>');
    expect($xml)->toContain('<image:geo_location>Netherlands</image:geo_location>');
});

it('merges two sitemaps into one', function () {
    $sitemapA = Sitemap::make([
        Url::make('https://example.com/page-a')
    ]);

    $sitemapB = Sitemap::make([
        Url::make('https://example.com/page-b')
    ]);

    $sitemapA->merge($sitemapB);

    $items = $sitemapA->toArray()['items'];

    expect($items)->toHaveCount(2);
    expect($items[0]['loc'])->toBe('https://example.com/page-a');
    expect($items[1]['loc'])->toBe('https://example.com/page-b');
});

it('loads URLs from registered providers', function () {
    $mock = Mockery::mock(SitemapProviderInterface::class);
    $mock->shouldReceive('getUrls')->once()->andReturn(
        collect([Url::make('https://example.com/from-provider')])
    );

    App::instance('dummy-provider', $mock);
    Sitemap::registerProvider('dummy-provider');

    $sitemap = Sitemap::fromProviders();

    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(1);
    expect($items[0]['loc'])->toBe('https://example.com/from-provider');
});