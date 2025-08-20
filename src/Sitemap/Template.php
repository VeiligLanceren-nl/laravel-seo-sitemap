<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Traversable;
use Illuminate\Routing\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\TestRouteNotSetException;

abstract class Template implements SitemapItemTemplate
{
    /**
     * @var Route|null
     */
    protected ?Route $testRoute = null;

    /**
     * Main method developers must implement.
     *
     * @param Route $route The route to which the template is bound.
     * @return iterable<Url>
     */
    abstract public function generate(Route $route): iterable;

    /**
     * Main method developers must implement.
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        if (!$this->testRoute) {
            throw new TestRouteNotSetException();
        }

        yield from $this->generate($this->testRoute);
    }

    /**
     * @param Route $route
     * @return void
     */
    public function setTestRoute(Route $route): void
    {
        $this->testRoute = $route;
    }

    /**
     * Helper for generating URLs from an Eloquent model/query.
     *
     * @template TModel of Model
     * @param class-string<TModel> $modelClass
     * @param Route $route
     * @param callable|null $callback function(TModel $model, Route $route): Url
     * @param Builder<TModel>|null $query Optionally provide a custom query (defaults to all records).
     * @param bool $useCursor Use cursor iteration for large datasets (default: true)
     * @param int|null $chunkSize Optional chunk size for chunked iteration (overrides cursor).
     * @return iterable<Url>
     */
    public function urlsFromModel(
        string $modelClass,
        Route $route,
        callable $callback = null,
        Builder $query = null,
        bool $useCursor = true,
        ?int $chunkSize = null
    ): iterable {
        $query = $query ?: $modelClass::query();

        if ($chunkSize && method_exists($query, 'chunk')) {
            $query->chunk($chunkSize, function ($models) use ($callback, $route, &$urls) {
                foreach ($models as $model) {
                    yield $callback
                        ? $callback($model, $route)
                        : Url::make(route($route->getName(), $model));
                }
            });

            return;
        }

        $items = $useCursor && method_exists($query, 'cursor')
            ? $query->cursor()
            : $query->get();

        foreach ($items as $model) {
            yield $callback
                ? $callback($model, $route)
                : Url::make(route($route->getName(), $model));
        }
    }

    /**
     * Helper for generating URLs from any iterable (e.g. arrays, collections, generators).
     *
     * @template TItem
     * @param iterable<TItem> $items
     * @param Route $route
     * @param callable(TItem $item, Route $route): Url $callback
     * @return iterable<Url>
     */
    public function urlsFromIterable(iterable $items, Route $route, callable $callback): iterable
    {
        foreach ($items as $item) {
            yield $callback($item, $route);
        }
    }

    /**
     * Helper for generating a single URL entry.
     *
     * @param string $url
     * @param callable|null $configure Optional callback to configure Url object.
     * @return Url
     */
    public function singleUrl(string $url, callable $configure = null): Url
    {
        $urlObj = Url::make($url);

        if ($configure) {
            $configure($urlObj);
        }

        return $urlObj;
    }

    /**
     * Helper for paginated resources.
     *
     * @param Route $route
     * @param int $totalItems
     * @param int $perPage
     * @param string $pageParam
     * @param array $extraParams
     * @param bool $skipPageOne
     * @return iterable<Url>
     */
    public function paginatedUrls(
        Route $route,
        int $totalItems,
        int $perPage = 20,
        string $pageParam = 'page',
        array $extraParams = [],
        bool $skipPageOne = false
    ): iterable {
        $totalPages = (int) ceil($totalItems / $perPage);

        for ($page = 1; $page <= $totalPages; ++$page) {
            if ($skipPageOne && $page === 1) {
                continue;
            }

            $url = route($route->getName(), array_merge($extraParams, [
                $pageParam => $page,
            ]));

            yield Url::make($url);
        }
    }
}