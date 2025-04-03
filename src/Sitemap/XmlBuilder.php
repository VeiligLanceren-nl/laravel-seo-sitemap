<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use SimpleXMLElement;
use Illuminate\Support\Collection;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;

class XmlBuilder
{
    /**
     * @param Collection $urls
     * @param array $options
     * @return string
     */
    public static function build(Collection $items, array $options = []): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        foreach ($items as $item) {
            if ($item instanceof Url) {
                $urlElement = $xml->addChild('url');
                foreach ($item->toArray() as $key => $value) {
                    if ($key === 'images') {
                        foreach ($item->getImages() as $image) {
                            $imageElement = $urlElement->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                            foreach ($image->toArray() as $imgKey => $imgVal) {
                                $imageElement->addChild("image:$imgKey", htmlspecialchars($imgVal), 'http://www.google.com/schemas/sitemap-image/1.1');
                            }
                        }
                    } else {
                        $urlElement->addChild($key, htmlspecialchars($value));
                    }
                }
            }

            if ($item instanceof Image) {
                // Optional: skip standalone Image or add as top-level <url>?
            }
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = $options['pretty'] ?? false;

        return $dom->saveXML();
    }
}