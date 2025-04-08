<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Http\Controllers\SitemapController;

use VeiligLanceren\LaravelSeoSitemap\Popo\RouteSitemapDefaults;
use function Pest\Laravel\get;

beforeEach(function () {
    Config::set('sitemap.file.path', 'sitemap.xml');
    Config::set('sitemap.file.disk', 'public');

    Route::get('/test', fn () => 'ok')
        ->name('test')
        ->sitemap()
        ->changefreq('weekly')
        ->priority('0.9');

    Route::get('/sitemap.xml', [SitemapController::class, 'index']);
});

it('returns the sitemap.xml file with correct headers when it exists', function () {
    Storage::fake('public');

    $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://example.com/</loc>
        <lastmod>2025-01-01</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
</urlset>
XML;

    Storage::disk('public')->put('sitemap.xml', $content);

    get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee($content, false);
});

it('returns 404 when sitemap.xml does not exist', function () {
    Storage::fake('public');

    get('/sitemap.xml')->assertNotFound();
});

it('can register sitemap metadata on a route', function () {
    $route = collect(Route::getRoutes()->getRoutes())
        ->firstWhere('uri', 'test');

    expect($route)->not->toBeNull();

    $defaults = $route->defaults;

    expect($defaults['sitemap'])->toBeInstanceOf(RouteSitemapDefaults::class)
        ->and($defaults['sitemap']->enabled)->toBeTrue()
        ->and($defaults['sitemap']->priority)->toBe('0.9')
        ->and($defaults['sitemap']->changefreq->value)->toBe('weekly');
});