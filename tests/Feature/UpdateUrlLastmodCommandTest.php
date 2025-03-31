<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use VeiligLanceren\LaravelSeoSitemap\Models\UrlMetadata;

it('updates lastmod for an existing route name', function () {
    UrlMetadata::create([
        'route_name' => 'test.route',
        'lastmod' => Carbon::parse('2023-01-01'),
    ]);

    Carbon::setTestNow(Carbon::parse('2025-03-31 15:00:00'));

    Artisan::call('url:update', [
        'routeName' => 'test.route',
    ]);

    $metadata = UrlMetadata::where('route_name', 'test.route')->first();

    expect($metadata)->not()->toBeNull()
        ->and($metadata->lastmod->toDateTimeString())->toBe('2025-03-31 15:00:00');
});

it('creates lastmod for a new route name', function () {
    expect(UrlMetadata::where('route_name', 'new.route')->exists())->toBeFalse();

    Carbon::setTestNow(Carbon::parse('2025-03-31 16:30:00'));

    Artisan::call('url:update', [
        'routeName' => 'new.route',
    ]);

    $metadata = UrlMetadata::where('route_name', 'new.route')->first();

    expect($metadata)->not()->toBeNull()
        ->and($metadata->lastmod->toDateTimeString())->toBe('2025-03-31 16:30:00');
});
