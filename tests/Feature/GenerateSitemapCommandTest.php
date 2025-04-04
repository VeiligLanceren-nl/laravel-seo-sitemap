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