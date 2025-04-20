<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

class DynamicRouteChild
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        protected array $parameters
    ) {}

    /**
     * @param array<string, mixed> $parameters
     * @return static
     */
    public static function make(array $parameters): static
    {
        return new static($parameters);
    }

    /**
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }
}
