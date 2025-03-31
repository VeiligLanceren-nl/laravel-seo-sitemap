<?php

use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Support\ChangeFrequency;
use VeiligLanceren\LaravelSeoSitemap\Url;

beforeEach(function () {
    Storage::fake('public');
});

it('creates a sitemap with loc only', function () {
    $sitemap = Sitemap::make([
        Url::make('https://example.com')
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'urls' => [['loc' => 'https://example.com']]
    ]);
});

it('creates a sitemap with loc and lastmod', function () {
    $sitemap = Sitemap::make([
        Url::make('https://example.com')->lastmod('2024-01-01')
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'urls' => [['loc' => 'https://example.com', 'lastmod' => '2024-01-01']]
    ]);
});

it('creates a sitemap with loc, lastmod, and changefreq', function () {
    $sitemap = Sitemap::make([
        Url::make('https://example.com')
            ->lastmod('2024-01-01')
            ->changefreq(ChangeFrequency::WEEKLY)
    ]);

    expect($sitemap->toArray())->toBe([
        'options' => [],
        'urls' => [[
            'loc' => 'https://example.com',
            'lastmod' => '2024-01-01',
            'changefreq' => 'weekly',
        ]]
    ]);

    $xml = $sitemap->toXml();

    expect($xml)->toContain('<changefreq>weekly</changefreq>');
});

it('creates pretty XML when enabled', function () {
    $sitemap = Sitemap::make([
        Url::make('https://example.com')->lastmod('2025-01-01')
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
        Url::make('https://example.com')
            ->lastmod('2025-01-01')
    ]);

    $sitemap->save('sitemap.xml', 'public');
    Storage::disk('public')->assertExists('sitemap.xml');

    $content = Storage::disk('public')->get('sitemap.xml');

    expect($content)->toContain('<loc>https://example.com</loc>');
});