<?php

namespace Tests\Support\Models;

class FakeCategory
{
    public function __construct(public string $slug) {}

    /**
     * @return string
     */
    public function getRouteKey(): string
    {
        return $this->slug;
    }
}