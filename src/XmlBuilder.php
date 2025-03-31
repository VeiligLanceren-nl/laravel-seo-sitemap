<?php

namespace VeiligLanceren\LaravelSeoSitemap;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class XmlBuilder
{
    /**
     * @param Collection $urls
     * @param array $options
     * @return string
     */
    public static function build(Collection $urls, array $options = []): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urls->getIterator() as $url) {
            $urlElement = $xml->addChild('url');

            foreach ($url->toArray() as $key => $value) {
                $urlElement->addChild($key, htmlspecialchars($value));
            }
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = $options['pretty'] ?? false;

        return $dom->saveXML();
    }
}