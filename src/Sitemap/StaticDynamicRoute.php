<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Illuminate\Support\Collection;

class StaticDynamicRoute extends DynamicRoute
{
    /**
     * @param iterable<DynamicRouteChild> $children
     */
    public function __construct(
        protected iterable $children
    ) {}

    /**
     * @return \Illuminate\Support\Collection<array<string, mixed>>
     */
    public function parameters(): Collection
    {
        return collect($this->children)->map(fn (DynamicRouteChild $child) => $child->parameters());
    }
}
