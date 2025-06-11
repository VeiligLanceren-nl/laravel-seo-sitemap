<?php

use Illuminate\Support\Facades\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

beforeEach(function () {
    Route::middleware([])->group(function () {
        Route::prefix('/blog')->group(function () {
            Route::get('/', fn () => 'blog index')
                ->name('support.blog.index')
                ->sitemap();

            Route::get('/{category}', fn () => 'blog category')
                ->name('support.blog.category')
                ->sitemap();

            Route::get('/{category}/{post}', fn () => 'blog post')
                ->name('support.blog.show')
                ->sitemapUsing(\Tests\Fixtures\SitemapTemplates\BlogPostTemplate::class);
        });
    });
});


it('generates sitemap XML with dynamic blog post URLs', function () {
    $sitemap = Sitemap::fromRoutes();
    $xml = $sitemap->toXml();

    expect($xml)->toContain('<loc>http://localhost/blog</loc>');
    expect($xml)->toContain('<loc>http://localhost/blog/ai</loc>');
    expect($xml)->toContain('<loc>http://localhost/blog/ai/how-to-use-laravel</loc>');
});
