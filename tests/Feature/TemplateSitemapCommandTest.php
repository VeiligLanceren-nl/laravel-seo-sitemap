<?php

use Illuminate\Console\BufferedConsoleOutput;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(app_path('SitemapTemplates'));
});

it('creates a new sitemap template class', function () {
    $exitCode = Artisan::call('sitemap:template', ['name' => 'BlogPostTemplate']);

    expect($exitCode)->toBe(0);

    $path = app_path('SitemapTemplates/BlogPostTemplate.php');
    expect(File::exists($path))->toBeTrue();
    expect(File::get($path))->toContain('class BlogPostTemplate');
});

it('does not overwrite an existing sitemap template class', function () {
    $path = app_path('SitemapTemplates/ExistingTemplate.php');
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'original');

    $output = new BufferedConsoleOutput();
    Artisan::call('sitemap:template', ['name' => 'ExistingTemplate'], $output);

    expect($output->fetch())->toContain('already exists');
    expect(File::get($path))->toBe('original');
});
