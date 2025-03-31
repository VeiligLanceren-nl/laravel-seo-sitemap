<?php

use Illuminate\Support\Collection;
use VeiligLanceren\LaravelSeoSitemap\Url;
use VeiligLanceren\LaravelSeoSitemap\XmlBuilder;

it('generates valid XML from URLs', function () {
    $urls = Collection::make([
        Url::make('https://example.com')
            ->lastmod('2024-01-01')
            ->priority('1.0'),

        Url::make('https://example.com/about')
            ->lastmod('2024-01-02'),
    ]);
    $builder = new XmlBuilder();
    $xml = $builder->build($urls);

    expect($xml)->toBeString();
    expect(simplexml_load_string($xml))->not()->toBeFalse();
    expect($xml)->toContain('<loc>https://example.com</loc>');
    expect($xml)->toContain('<lastmod>2024-01-01</lastmod>');
    expect($xml)->toContain('<priority>1.0</priority>');
});

it('respects pretty option in XML output', function () {
    $url = Url::make('https://example.com');
    $builder = new XmlBuilder();

    $xml = $builder->build(Collection::make([$url]));

    expect($xml)->toContain("\n");
});
