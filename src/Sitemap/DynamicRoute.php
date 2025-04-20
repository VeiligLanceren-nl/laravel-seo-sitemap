<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Illuminate\Support\Collection;

abstract class DynamicRoute
{
    /**
     * @return Collection<array<string, mixed>>
     */
    abstract public function parameters(): Collection;
}
