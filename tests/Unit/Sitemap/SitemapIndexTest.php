<?php

use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

beforeEach(function () {
    Storage::fake('public');
});

it('creates a sitemap index with multiple entries', function () {
    $index = SitemapIndex::make('https://example.com/sitemap-a.xml')
        ->add('https://example.com/sitemap-b.xml', '2024-01-01');

    $array = $index->toArray();

    expect($array['sitemaps'])->toBe([
        ['loc' => 'https://example.com/sitemap-a.xml'],
        ['loc' => 'https://example.com/sitemap-b.xml', 'lastmod' => '2024-01-01'],
    ]);
});

it('generates xml without lastmod when not provided', function () {
    $index = SitemapIndex::make('https://example.com/sitemap-a.xml');

    $xml = $index->toXml();

    expect($xml)->toContain('<loc>https://example.com/sitemap-a.xml</loc>');
    expect($xml)->not->toContain('<lastmod>');
});

it('generates xml with lastmod when provided', function () {
    $index = SitemapIndex::make('https://example.com/sitemap-a.xml', '2024-01-01');

    $xml = $index->toXml();

    expect($xml)->toContain('<loc>https://example.com/sitemap-a.xml</loc>');
    expect($xml)->toContain('<lastmod>2024-01-01</lastmod>');
});

it('saves the sitemap index to disk', function () {
    $index = SitemapIndex::make('https://example.com/sitemap-a.xml')
        ->add('https://example.com/sitemap-b.xml', '2024-01-01');

    Storage::disk('public')->put('sitemap.xml', $index->toXml());

    Storage::disk('public')->assertExists('sitemap.xml');
    $content = Storage::disk('public')->get('sitemap.xml');

    expect($content)->toContain('<loc>https://example.com/sitemap-a.xml</loc>');
    expect($content)->toContain('<loc>https://example.com/sitemap-b.xml</loc>');
    expect($content)->toContain('<lastmod>2024-01-01</lastmod>');
});
