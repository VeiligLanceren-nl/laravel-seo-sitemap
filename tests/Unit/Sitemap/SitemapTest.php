<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;
use VeiligLanceren\LaravelSeoSitemap\Popo\Sitemap\Item\UrlAttributes;

beforeEach(function () {
    File::deleteDirectory(public_path('sitemaps'));
    Storage::fake('public');
});

test('it can write sitemap to disk', function () {
    $sitemap = new Sitemap();

    $sitemap->add(
        Url::make(new UrlAttributes(
            '/contact',
            '2023-01-01',
            ChangeFrequency::WEEKLY,
            0.7
        ))
    );

    $sitemap->save('sitemaps/pages.xml', 'public');

    Storage::disk('public')->assertExists('sitemaps/pages.xml');

    $content = Storage::disk('public')->get('sitemaps/pages.xml');
    expect($content)->toContain('<loc>http://localhost/contact</loc>');
});

test('it can add images to url item', function () {
    $url = Url::make(new UrlAttributes(
        '/with-images',
        '2023-01-01',
        ChangeFrequency::MONTHLY
    ));

    $url->addImage(Image::make('https://example.com/image1.jpg'));
    $url->addImage(Image::make('https://example.com/image2.jpg'));

    expect($url->getImages())->toHaveCount(2);
});

test('it can build URL using UrlAttributes only', function () {
    $attributes = new UrlAttributes(
        '/news',
        '2023-04-01',
        ChangeFrequency::DAILY,
        0.9,
        ['source' => 'resources/views/news.blade.php']
    );

    $url = Url::make($attributes);

    expect($url->toArray())->toBe([
        'loc' => url('/news'),
        'lastmod' => '2023-04-01',
        'priority' => '0.9',
        'changefreq' => ChangeFrequency::DAILY,
    ]);
});

test('it prefers arguments over attributes if both are passed', function () {
    $attributes = new UrlAttributes(
        '/news',
        '2023-04-01',
        ChangeFrequency::DAILY,
        0.9
    );

    $url = Url::make(
        loc: '/overridden',
        lastmod: '2024-01-01',
        priority: '0.6',
        changeFrequency: ChangeFrequency::WEEKLY,
        attributes: $attributes
    );

    expect($url->toArray())->toBe([
        'loc' => url('/overridden'),
        'lastmod' => '2024-01-01',
        'priority' => '0.6',
        'changefreq' => ChangeFrequency::WEEKLY,
    ]);
});
