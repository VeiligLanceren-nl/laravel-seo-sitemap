<?php

use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

beforeEach(function () {
    Storage::fake('public');
});

it('creates a sitemap index with multiple entries', function () {
    $index = SitemapIndex::make([
        'https://example.com/sitemap-a.xml',
        'https://example.com/sitemap-b.xml',
    ]);

    $array = $index->toArray();

    expect($array['sitemaps'])->toBe([
        'https://example.com/sitemap-a.xml',
        'https://example.com/sitemap-b.xml',
    ]);
});

it('generates valid sitemap index XML', function () {
    $index = SitemapIndex::make([
        'https://example.com/sitemap-a.xml',
        'https://example.com/sitemap-b.xml',
    ], [
        'pretty' => true
    ]);

    $xml = $index->toXml();

    expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
    expect($xml)->toContain('<sitemapindex');
    expect($xml)->toContain('<loc>https://example.com/sitemap-a.xml</loc>');
    expect($xml)->toContain('<loc>https://example.com/sitemap-b.xml</loc>');
});

it('saves the sitemap index to disk', function () {
    $index = SitemapIndex::make([
        'https://example.com/sitemap-a.xml',
        'https://example.com/sitemap-b.xml',
    ]);

    Storage::disk('public')->put('sitemap.xml', $index->toXml());

    Storage::disk('public')->assertExists('sitemap.xml');
    $content = Storage::disk('public')->get('sitemap.xml');

    expect($content)->toContain('<loc>https://example.com/sitemap-a.xml</loc>');
    expect($content)->toContain('<loc>https://example.com/sitemap-b.xml</loc>');
});