<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Storage::fake('public');

    Route::get('/test-sitemap-command', fn () => 'Test')
        ->name('test.sitemap')
        ->sitemap();
});

it('generates and saves sitemap.xml to default disk and path from config', function () {
    Config::set('sitemap.file.disk', 'public');
    Config::set('sitemap.file.path', 'sitemap.xml');

    $exitCode = Artisan::call('sitemap:generate');

    expect($exitCode)->toBe(0);
    Storage::disk('public')->assertExists('sitemap.xml');

    $content = Storage::disk('public')->get('sitemap.xml');
    expect($content)->toContain('<loc>' . url('/test-sitemap-command') . '</loc>');
});

it('generates and saves sitemap.xml to disk when CLI options are passed', function () {
    $path = 'custom-path.xml';

    $exitCode = Artisan::call('sitemap:generate', [
        '--path' => $path,
        '--disk' => 'public',
    ]);

    expect($exitCode)->toBe(0);

    Storage::disk('public')->assertExists($path);
    $content = Storage::disk('public')->get($path);

    expect($content)->toContain('<loc>' . url('/test-sitemap-command') . '</loc>');
});

it('generates pretty XML when --pretty is passed', function () {
    $path = 'sitemap-pretty.xml';

    Artisan::call('sitemap:generate', [
        '--path' => $path,
        '--disk' => 'public',
        '--pretty' => true,
    ]);

    Storage::disk('public')->assertExists($path);

    $content = Storage::disk('public')->get($path);

    expect($content)->toContain("\n");
    expect($content)->toContain('<urlset');
    expect($content)->toContain('<loc>' . url('/test-sitemap-command') . '</loc>');
});

it('generates a sitemap index when routes use indexes', function () {
    Storage::fake('public');

    Route::get('/alpha', fn () => 'Alpha')
        ->name('alpha')
        ->sitemapIndex('alpha');

    Route::get('/beta', fn () => 'Beta')
        ->name('beta')
        ->sitemapIndex('beta');

    Artisan::call('sitemap:generate');

    Storage::disk('public')->assertExists('sitemap.xml');
    Storage::disk('public')->assertExists('sitemap-alpha.xml');
    Storage::disk('public')->assertExists('sitemap-beta.xml');
    Storage::disk('public')->assertExists('sitemap-default.xml');

    $index = Storage::disk('public')->get('sitemap.xml');
    expect($index)->toContain('<loc>' . url('/sitemap-alpha.xml') . '</loc>');
    expect($index)->toContain('<loc>' . url('/sitemap-beta.xml') . '</loc>');
    expect($index)->toContain('<loc>' . url('/sitemap-default.xml') . '</loc>');

    $alpha = Storage::disk('public')->get('sitemap-alpha.xml');
    expect($alpha)->toContain('<loc>' . url('/alpha') . '</loc>');

    $beta = Storage::disk('public')->get('sitemap-beta.xml');
    expect($beta)->toContain('<loc>' . url('/beta') . '</loc>');

    $default = Storage::disk('public')->get('sitemap-default.xml');
    expect($default)->toContain('<loc>' . url('/test-sitemap-command') . '</loc>');
});