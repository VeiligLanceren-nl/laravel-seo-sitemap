<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Tester\CommandTester;
use VeiligLanceren\LaravelSeoSitemap\Console\Commands\TemplateSitemap;

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

    $command = resolve(TemplateSitemap::class);
    $command->setLaravel(app());
    $tester = new CommandTester($command);
    $tester->execute(['name' => 'ExistingTemplate'], ['capture_stderr_separately' => true]);

    $output = $tester->getDisplay() . $tester->getErrorOutput();

    expect($output)->toContain('already exists');
    expect(File::get($path))->toBe('original');
});
