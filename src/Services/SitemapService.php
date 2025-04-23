<?php

namespace VeiligLanceren\LaravelSeoSitemap\Services;

use Illuminate\Support\HtmlString;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Sitemap;

class SitemapService
{
    /**
     * @var Sitemap
     */
    protected Sitemap $sitemap;

    /**
     * @param Sitemap $sitemap
     */
    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    /**
     * Generate a meta tag referencing the sitemap.xml
     *
     * @param string|null $url
     * @return HtmlString
     */
    public static function meta(?string $url = null): HtmlString
    {
        $sitemapUrl = $url ?? url('/sitemap.xml');

        return new HtmlString(
            sprintf('<meta name="sitemap" content="%s" />', e($sitemapUrl))
        );
    }

    /**
     * @return $this
     */
    public function fromRoutes(): self
    {
        $this->sitemap->fromRoutes();

        return $this;
    }

    /**
     * @return Sitemap
     */
    public function getSitemap(): Sitemap
    {
        return $this->sitemap;
    }
}