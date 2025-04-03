<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

abstract class SitemapItem
{
    /**
     * Convert the item to an array.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}