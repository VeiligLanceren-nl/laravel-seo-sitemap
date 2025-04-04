<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\Support\Models\FakeCategory;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

it('includes only GET routes with sitemap default', function () {
    Route::get('/included', fn () => 'included')
        ->name('included')
        ->sitemap()
        ->priority('0.8');

    Route::post('/excluded', fn () => 'excluded')->name('excluded')->sitemap();

    $sitemap = Sitemap::fromRoutes();
    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(1);
    expect($items[0]['loc'])->toBe(URL::to('/included'));
    expect($items[0]['priority'])->toBe('0.8');
});

it('includes routes with multiple parameter values', function () {
    $categories = ['tech', 'design', 'marketing'];

    Route::get('/category/{slug}', fn ($slug) => "Category: $slug")
        ->name('category.show')
        ->sitemap(['slug' => $categories])
        ->priority('0.5');

    $sitemap = Sitemap::fromRoutes();
    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(count($categories));

    foreach ($categories as $index => $slug) {
        expect($items[$index]['loc'])->toBe(URL::to("/category/$slug"));
        expect($items[$index]['priority'])->toBe('0.5');
    }
});

it('includes model parameters in sitemap', function () {
    $models = collect([
        new FakeCategory('ai'),
        new FakeCategory('design'),
        new FakeCategory('laravel'),
    ]);

    Route::get('/category/{category}', fn (FakeCategory $category) => "Category: {$category->slug}")
        ->name('category.show')
        ->sitemap(['category' => $models])
        ->priority('0.6');

    $sitemap = Sitemap::fromRoutes();
    $items = $sitemap->toArray()['items'];

    expect($items)->toHaveCount(3);

    foreach ($models as $index => $model) {
        expect($items[$index]['loc'])->toBe(URL::to("/category/{$model->getRouteKey()}"));
        expect($items[$index]['priority'])->toBe('0.6');
    }
});