<?php

use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

it('can be created using the make factory method', function () {
    $image = Image::make('https://example.com/image.jpg');

    expect($image->toArray())->toBe([
        'loc' => 'https://example.com/image.jpg',
    ]);
});

it('sets all optional fields fluently', function () {
    $image = Image::make('https://example.com/photo.jpg')
        ->caption('A beautiful view')
        ->title('Sunset')
        ->license('https://example.com/license')
        ->geoLocation('Amsterdam, Netherlands');

    expect($image->toArray())->toMatchArray([
        'loc' => 'https://example.com/photo.jpg',
        'caption' => 'A beautiful view',
        'title' => 'Sunset',
        'license' => 'https://example.com/license',
        'geo_location' => 'Amsterdam, Netherlands',
    ]);
});

it('filters out null values in toArray', function () {
    $image = (new Image())->loc('https://example.com/img.png');

    expect($image->toArray())->toBe([
        'loc' => 'https://example.com/img.png',
    ]);
});

it('allows a URL to contain multiple images', function () {
    $url = Url::make('https://example.com')
        ->addImage(Image::make('https://example.com/image1.jpg')->title('Image 1'))
        ->addImage(Image::make('https://example.com/image2.jpg')->title('Image 2'));

    $sitemap = Sitemap::make([$url]);

    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(1);
    expect($items[0]['loc'])->toBe('https://example.com');
    expect($items[0]['images'])->toHaveCount(2);
    expect($items[0]['images'][0]['title'])->toBe('Image 1');
    expect($items[0]['images'][1]['title'])->toBe('Image 2');

    $xml = $sitemap->toXml();

    expect($xml)->toContain('<image:image');
    expect($xml)->toContain('<image:loc>https://example.com/image1.jpg</image:loc>');
    expect($xml)->toContain('<image:title>Image 1</image:title>');
    expect($xml)->toContain('<image:loc>https://example.com/image2.jpg</image:loc>');
    expect($xml)->toContain('<image:title>Image 2</image:title>');
});