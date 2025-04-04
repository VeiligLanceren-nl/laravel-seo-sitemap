<?php

use Illuminate\Support\Collection;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\XmlBuilder;

it('generates valid XML from URLs', function () {
    $urls = Collection::make([
        Url::make('https://example.com')
            ->lastmod('2024-01-01')
            ->priority('1.0'),

        Url::make('https://example.com/about')
            ->lastmod('2024-01-02'),
    ]);

    $xml = XmlBuilder::build($urls);

    expect($xml)->toBeString();
    expect(simplexml_load_string($xml))->not()->toBeFalse();
    expect($xml)->toContain('<loc>https://example.com</loc>');
    expect($xml)->toContain('<lastmod>2024-01-01</lastmod>');
    expect($xml)->toContain('<priority>1.0</priority>');
});

it('respects pretty option in XML output', function () {
    $url = Url::make('https://example.com');

    $xml = XmlBuilder::build(Collection::make([$url]), ['pretty' => true]);

    expect($xml)->toContain("\n");
});

it('includes <image:image> when url has images', function () {
    $url = Url::make('https://example.com/product')
        ->addImage(Image::make('https://example.com/image.jpg')->caption('Product Image'));

    $xml = XmlBuilder::build(Collection::make([$url]));

    expect($xml)->toContain('<image:image');
    expect($xml)->toContain('<image:loc>https://example.com/image.jpg</image:loc>');
    expect($xml)->toContain('<image:caption>Product Image</image:caption>');
});

it('generates standalone image <url> blocks for Image items', function () {
    $image = Image::make('https://example.com/standalone.jpg')
        ->caption('Standalone Image');

    $xml = XmlBuilder::build(Collection::make([$image]));

    expect($xml)->toContain('<url>');
    expect($xml)->toContain('<loc>https://example.com/standalone.jpg</loc>');
    expect($xml)->toContain('<image:image');
    expect($xml)->toContain('<image:loc>https://example.com/standalone.jpg</image:loc>');
    expect($xml)->toContain('<image:caption>Standalone Image</image:caption>');
});
