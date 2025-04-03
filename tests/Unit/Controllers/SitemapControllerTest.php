<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Response;
use VeiligLanceren\LaravelSeoSitemap\Http\Controllers\SitemapController;

beforeEach(function () {
    Config::set('sitemap.file.path', 'sitemap.xml');
    Config::set('sitemap.file.disk', 'public');
});

it('returns the XML response when sitemap exists', function () {
    $content = '<urlset></urlset>';

    Storage::shouldReceive('disk')
        ->with('public')
        ->twice()
        ->andReturnSelf();

    Storage::shouldReceive('exists')
        ->with('sitemap.xml')
        ->once()
        ->andReturn(true);

    Storage::shouldReceive('get')
        ->with('sitemap.xml')
        ->once()
        ->andReturn($content);

    $controller = new SitemapController();
    $response = $controller->index();

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
    expect($response->headers->get('Content-Type'))->toBe('application/xml');
    expect($response->getContent())->toBe($content);
});

it('throws 404 when sitemap does not exist', function () {
    Storage::shouldReceive('disk')
        ->with('public')
        ->once()
        ->andReturnSelf();

    Storage::shouldReceive('exists')
        ->with('sitemap.xml')
        ->once()
        ->andReturn(false);

    $controller = new SitemapController();

    $this->expectException(Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

    $controller->index();
});