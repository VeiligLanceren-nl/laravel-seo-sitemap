<?php

use Illuminate\Http\Request;
use Tests\Support\Models\DummyModel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route as LaravelRoute;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Template;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\TestRouteNotSetException;

beforeEach(function () {
    Schema::create('dummy_models', function (Blueprint $table) {
        $table->id();
        $table->string('slug');
        $table->timestamps();
    });

    Route::get('/stub/{page?}', fn () => 'ok')->name('stub.route');
    Route::get('/items/{item?}', fn () => 'ok')->name('items.route');
    Route::get('/model/{slug}', fn () => 'ok')->name('model.route');

    $this->stubRoute   = Route::getRoutes()->match(Request::create('/stub/1', 'GET'));
    $this->itemsRoute  = Route::getRoutes()->match(Request::create('/items/1', 'GET'));
    $this->modelRoute  = Route::getRoutes()->match(Request::create('/model/test-slug', 'GET'));

    $this->template = new class extends Template {
        public function generate(LaravelRoute $route): iterable
        {
            yield Url::make('https://example.com/one');
            yield Url::make('https://example.com/two');
        }
    };
});

afterEach(function () {
    Schema::dropIfExists('dummy_models');
});

it('throws if test route is not set before iteration', function () {
    expect(fn () => iterator_to_array($this->template->getIterator()))
        ->toThrow(TestRouteNotSetException::class);
});

it('can iterate over generate results using getIterator', function () {
    $this->template->setTestRoute($this->stubRoute);

    $results = iterator_to_array($this->template->getIterator());

    expect($results)->toHaveCount(2)
        ->and($results[0]->toArray()['loc'])->toBe('https://example.com/one');
});

it('can generate URLs from an iterable', function () {
    $items = [1, 2, 3];

    $urls = iterator_to_array(
        $this->template->urlsFromIterable($items, $this->itemsRoute, fn ($item, $route) =>
        Url::make(route($route->getName(), ['item' => $item]))
        )
    );

    expect($urls)->toHaveCount(3)
        ->and($urls[2]->toArray()['loc'])->toContain('/items/3');
});

it('can generate a single Url object', function () {
    $url = $this->template->singleUrl('https://example.com/foo', fn (Url $url) =>
    $url->lastmod('2025-01-01')
    );

    expect($url)->toBeInstanceOf(Url::class)
        ->and($url->toArray()['lastmod'])->toBe('2025-01-01');
});

it('can generate paginated URLs', function () {
    $urls = iterator_to_array($this->template->paginatedUrls($this->stubRoute, 45, 10));

    expect($urls)->toHaveCount(5)
        ->and($urls[0]->toArray()['loc'])->toContain('/stub/1');
});

it('can skip page one in paginated URLs', function () {
    $urls = iterator_to_array($this->template->paginatedUrls($this->stubRoute, 19, 10, 'page', [], true));

    expect($urls)->toHaveCount(1)
        ->and($urls[0]->toArray()['loc'])->toContain('/stub/2');
});

it('can generate URLs from an Eloquent model', function () {
    DummyModel::create(['slug' => 'foo']);
    DummyModel::create(['slug' => 'bar']);

    $urls = iterator_to_array($this->template->urlsFromModel(DummyModel::class, $this->modelRoute));

    expect($urls)->toHaveCount(2)
        ->and($urls[0])->toBeInstanceOf(Url::class)
        ->and($urls[0]->toArray()['loc'])->toContain('/model/foo');
});

it('can generate model URLs using a custom callback', function () {
    DummyModel::create(['slug' => 'custom']);

    $urls = iterator_to_array($this->template->urlsFromModel(
        DummyModel::class,
        $this->modelRoute,
        fn (DummyModel $model, $route) =>
        Url::make('https://custom.test/' . $model->slug)
    ));

    expect($urls)->toHaveCount(1)
        ->and($urls[0]->toArray()['loc'])->toBe('https://custom.test/custom');
});

it('can generate model URLs using cursor iteration', function () {
    DummyModel::factory()->count(3)->create();

    $urls = iterator_to_array($this->template->urlsFromModel(
        DummyModel::class,
        $this->modelRoute,
    ));

    expect($urls)->toHaveCount(3);
});
