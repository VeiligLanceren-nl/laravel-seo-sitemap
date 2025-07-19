<?php


namespace Tests\Support\Sitemap\Templates;

use Illuminate\Routing\Route;
use Tests\Support\Models\DummyModel;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Template;

class AbstractTestTemplate extends Template
{
    /**
     * @param Route $route
     * @return iterable<Url>
     */
    public function generate(Route $route): iterable
    {
        yield from $this->urlsFromModel(DummyModel::class, $route);
    }
}