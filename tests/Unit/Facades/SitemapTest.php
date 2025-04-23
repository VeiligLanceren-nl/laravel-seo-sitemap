<?php

use Illuminate\Support\HtmlString;
use VeiligLanceren\LaravelSeoSitemap\Services\SitemapService;
use VeiligLanceren\LaravelSeoSitemap\Facades\Sitemap as SitemapFacade;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap as SitemapObject;

it('calls fromRoutes through the facade', function () {
    $mockSitemap = Mockery::mock(SitemapObject::class);

    $mockService = Mockery::mock(SitemapService::class);
    $mockService->shouldReceive('fromRoutes')->once()->andReturn($mockService);
    $mockService->shouldReceive('getSitemap')->once()->andReturn($mockSitemap);

    app()->instance(SitemapService::class, $mockService);

    $result = SitemapFacade::fromRoutes()->getSitemap();

    expect($result)->toBe($mockSitemap);
});

it('generates meta tag through the facade using default URL', function () {
    $html = SitemapFacade::meta();

    expect($html)->toBeInstanceOf(HtmlString::class);
    expect((string) $html)->toBe('<meta name="sitemap" content="' . url('/sitemap.xml') . '" />');
});

it('generates meta tag through the facade using custom URL', function () {
    $customUrl = 'https://cdn.example.com/static/sitemap.xml';

    $html = SitemapFacade::meta($customUrl);

    expect($html)->toBeInstanceOf(HtmlString::class);
    expect((string) $html)->toBe('<meta name="sitemap" content="' . $customUrl . '" />');
});
