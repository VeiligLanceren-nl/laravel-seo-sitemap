<?php

namespace Tests\Support\Sitemap\ItemTemplate;

use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Support\Traits\HasPaginatedSitemap;

class DummySitemapTemplate
{
    use HasPaginatedSitemap;

    /**
     * @param Route $route
     * @param int $total
     * @param int $per
     * @param array $extra
     * @param bool $skipOne
     * @return array
     */
    public function getUrls(Route $route, int $total, int $per = 2, array $extra = [], bool $skipOne = false): array
    {
        return iterator_to_array($this->paginatedUrls($route, $total, $per, 'page', $extra, $skipOne));
    }
}