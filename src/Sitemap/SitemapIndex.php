<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Exception;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class SitemapIndex
{
    /**
     * @var Collection<string>
     */
    protected Collection $locations;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @param array<string> $locations
     * @param array $options
     * @return static
     */
    public static function make(array $locations = [], array $options = []): static
    {
        $instance = new static();
        $instance->locations = collect($locations);
        $instance->options = $options;

        return $instance;
    }

    /**
     * @param string $loc
     * @return $this
     */
    public function add(string $loc): static
    {
        $this->locations->push($loc);

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function toXml(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex/>', LIBXML_NOERROR | LIBXML_ERR_NONE);
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->locations as $loc) {
            $sitemap = $xml->addChild('sitemap');
            $sitemap->addChild('loc', htmlspecialchars($loc));
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = $this->options['pretty'] ?? false;

        return $dom->saveXML();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'options' => $this->options,
            'sitemaps' => $this->locations->all(),
        ];
    }
}
