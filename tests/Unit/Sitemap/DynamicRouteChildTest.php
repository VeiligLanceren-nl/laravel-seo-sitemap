<?php

use VeiligLanceren\LaravelSeoSitemap\Sitemap\DynamicRouteChild;

it('creates a DynamicRouteChild and returns correct parameters', function () {
    $params = ['slug' => 'example', 'category' => 'news'];
    $child = DynamicRouteChild::make($params);

    expect($child->parameters())->toBe($params)
        ->and($child->parameters())->toHaveKey('slug')
        ->and($child->parameters()['slug'])->toBe('example');
});

it('supports empty parameter array', function () {
    $child = DynamicRouteChild::make([]);

    expect($child->parameters())->toBeArray()
        ->and($child->parameters())->toBeEmpty();
});
