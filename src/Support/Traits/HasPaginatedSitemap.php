<?php

namespace VeiligLanceren\LaravelSeoSitemap\Support\Traits;

use Traversable;
use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;

trait HasPaginatedSitemap
{
    /**
     * Generate paginated URLs for a resource index.
     *
     * @param Route $route
     * @param int $totalItems
     * @param int $perPage
     * @param string $pageParam
     * @param array $extraParams Extra route parameters to merge in (optional)
     * @param bool $skipPageOne If true, do not include ?page=1 (default: false)
     * @return Traversable<Url>
     */
    protected function paginatedUrls(
        Route $route,
        int $totalItems,
        int $perPage = 20,
        string $pageParam = 'page',
        array $extraParams = [],
        bool $skipPageOne = false
    ): Traversable {
        $totalPages = (int) ceil($totalItems / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            if ($skipPageOne && $page === 1) {
                continue;
            }

            $params = array_merge($extraParams, [$pageParam => $page]);

            yield Url::make(route($route->getName(), $params));
        }
    }
}