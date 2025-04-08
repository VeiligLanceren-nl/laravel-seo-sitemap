<?php

if (!function_exists('sitemap_meta_tag')) {
    /**
     * Generate a meta tag referencing the sitemap.xml
     *
     * @param string|null $url
     * @return string
     */
    function sitemap_meta_tag(?string $url = null): string
    {
        $sitemapUrl = $url ?? url('/sitemap.xml');

        return sprintf('<meta name="sitemap" content="%s" />', e($sitemapUrl));
    }
}