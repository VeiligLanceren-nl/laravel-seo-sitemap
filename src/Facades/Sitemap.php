<?php

namespace VeiligLanceren\LaravelSeoSitemap\Facades;

use Illuminate\Support\Facades\Facade;

class Sitemap extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \VeiligLanceren\LaravelSeoSitemap\Sitemap::class;
    }
}