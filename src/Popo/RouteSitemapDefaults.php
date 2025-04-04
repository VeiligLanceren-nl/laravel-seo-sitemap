<?php

namespace VeiligLanceren\LaravelSeoSitemap\Popo;

use Scrumble\Popo\BasePopo;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class RouteSitemapDefaults extends BasePopo
{
    /**
     * @var bool
     */
    public bool $enabled = false;

    /**
     * @var array<string, string[]>
     */
    public array $parameters = [];

    /**
     * @var float|null
     */
    public ?string $priority = null;

    /**
     * @var ChangeFrequency|null
     */
    public ?ChangeFrequency $changefreq = null;
}