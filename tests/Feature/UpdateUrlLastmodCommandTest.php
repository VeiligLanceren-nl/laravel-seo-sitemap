<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;
use VeiligLanceren\LaravelSeoSitemap\Services\Ping\GooglePingService;

uses()->beforeEach(function () {
    $mock = Mockery::mock(GooglePingService::class);
    $mock->shouldReceive('ping')->andReturnTrue();

    App::instance(GooglePingService::class, $mock);
});

it('updates lastmod for an existing route name', function () {
    $path = base_path('tests/Fixtures/test-template.blade.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'Hello');
    touch($path, strtotime('2025-03-31 15:00:00'));

    Storage::disk('public')->put('sitemaps/pages.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/test</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta>
            <route>test.route</route>
            <source>tests/Fixtures/test-template.blade.php</source>
        </meta>
    </url>
</urlset>
XML));


    $this->artisan('sitemap:update', ['routeName' => 'test.route', '--no-ping' => true]);

    $metadata = UrlMetadata::query()
        ->where('route_name', 'test.route')
        ->first();

    expect($metadata)
        ->not()
        ->toBeNull()
        ->and($metadata->lastmod->toDateTimeString())
        ->toBe('2025-03-31 15:00:00');
});
it('creates lastmod for a new route name', function () {
    expect(
        UrlMetadata::query()
            ->where('route_name', 'new.route')
            ->exists()
    )->toBeFalse();

    Carbon::setTestNow(Carbon::parse('2025-03-31 16:30:00'));
    Storage::disk('public')->put('sitemaps/new.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/new</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta>
            <route>new.route</route>
            <source>tests/Fixtures/new-template.blade.php</source>
        </meta>
    </url>
</urlset>
XML));

    File::put(base_path('tests/Fixtures/new-template.blade.php'), 'Hello');
    touch(base_path('tests/Fixtures/new-template.blade.php'), strtotime('2025-03-31 16:30:00'));

    Artisan::call('sitemap:update', [
        'routeName' => 'new.route',
    ]);

    $metadata = UrlMetadata::query()
        ->where('route_name', 'new.route')
        ->first();

    expect($metadata)->not()->toBeNull()
        ->and($metadata->lastmod->toDateTimeString())->toBe('2025-03-31 16:30:00');
});

it('does nothing if the routeName does not exist in any sitemap', function () {
    Storage::disk('public')->put('sitemaps/pages.xml', ltrim(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>/about</loc>
        <lastmod>2023-01-01T00:00:00+00:00</lastmod>
        <meta>
            <route>about.route</route>
            <source>tests/Fixtures/about.blade.php</source>
        </meta>
    </url>
</urlset>
XML));

    Artisan::call('sitemap:update', [
        'routeName' => 'missing.route',
        '--no-ping' => true,
    ]);

    expect(UrlMetadata::where('route_name', 'missing.route')->exists())->toBeFalse();
});

