<?php

use Illuminate\Support\HtmlString;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Services\SitemapService;

it('calls fromRoutes and returns itself', function () {
    $mock = Mockery::mock(Sitemap::class);
    $mock->shouldReceive('fromRoutes')->once();

    $service = new SitemapService($mock);

    $result = $service->fromRoutes();

    expect($result)->toBeInstanceOf(SitemapService::class);
});

it('returns the internal Sitemap instance', function () {
    $mock = Mockery::mock(Sitemap::class);

    $service = new SitemapService($mock);

    expect($service->getSitemap())->toBe($mock);
});

it('generates a meta tag with the default sitemap URL', function () {
    $tag = SitemapService::meta();

    expect($tag)->toBeInstanceOf(HtmlString::class);
    expect((string) $tag)->toBe('<meta name="sitemap" content="' . url('/sitemap.xml') . '" />');
});

it('generates a meta tag with a custom sitemap URL', function () {
    $url = 'https://cdn.example.com/static/sitemap.xml';
    $tag = SitemapService::meta($url);

    expect($tag)->toBeInstanceOf(HtmlString::class);
    expect((string) $tag)->toBe('<meta name="sitemap" content="' . $url . '" />');
});