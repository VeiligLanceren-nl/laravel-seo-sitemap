<?php

it('generates default sitemap meta tag when no URL is given', function () {
    $expected = '<meta name="sitemap" content="' . e(url('/sitemap.xml')) . '" />';
    expect(sitemap_meta_tag())->toBe($expected);
});

it('generates sitemap meta tag with custom URL', function () {
    $customUrl = 'https://example.com/custom-sitemap.xml';
    $expected = '<meta name="sitemap" content="' . e($customUrl) . '" />';
    expect(sitemap_meta_tag($customUrl))->toBe($expected);
});
