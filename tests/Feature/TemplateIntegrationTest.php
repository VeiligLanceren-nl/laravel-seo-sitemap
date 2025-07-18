<?php

use Tests\Support\Models\DummyModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;
use Tests\Support\Sitemap\Templates\AbstractTestTemplate;

beforeEach(function () {
    Schema::create('dummy_models', function (Blueprint $table) {
        $table->id();
        $table->string('slug');
        $table->timestamps();
    });

    DummyModel::create(['slug' => 'niels']);
    DummyModel::create(['slug' => 'veilig-lanceren']);

    Route::get('/abstract/{slug}', fn () => 'ok')
        ->name('abstract.route')
        ->sitemapUsing(AbstractTestTemplate::class);
});

afterEach(function () {
    Schema::dropIfExists('dummy_models');
});

it('resolves the Template abstract and generates URLs through sitemapUsing', function () {
    $sitemap = Sitemap::fromRoutes();

    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(2)
        ->and($items[0]['loc'])->toBe(url('/abstract/niels'))
        ->and($items[1]['loc'])->toBe(url('/abstract/veilig-lanceren'));
});
