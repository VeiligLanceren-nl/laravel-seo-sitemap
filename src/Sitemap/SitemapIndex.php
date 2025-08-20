<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use DateTimeInterface;
use Exception;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class SitemapIndex
{
    /**
     * @var Collection<SitemapIndexEntry>
     */
    protected Collection $locations;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @param string|null $loc
     * @param DateTimeInterface|string|null $lastmod
     * @param array $options
     * @return static
     */
    public static function make(
        string $loc = null,
        DateTimeInterface|string $lastmod = null,
        array $options = [],
    ): static {
        $instance = new static();
        $instance->locations = collect();
        $instance->options = $options;

        if ($loc) {
            $instance->add($loc, $lastmod);
        }

        return $instance;
    }

    /**
     * @param string $loc
     * @param DateTimeInterface|string|null $lastmod
     * @return $this
     */
    public function add(string $loc, DateTimeInterface|string $lastmod = null): static
    {
        $this->locations->push(new SitemapIndexEntry($loc, $lastmod));

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

        foreach ($this->locations as $entry) {
            $sitemap = $xml->addChild('sitemap');
            $sitemap->addChild('loc', htmlspecialchars($entry->getLoc()));

            if ($entry->getLastmod()) {
                $sitemap->addChild('lastmod', $entry->getLastmod());
            }
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
            'sitemaps' => $this->locations
                ->map(fn(SitemapIndexEntry $entry) => $entry->toArray())
                ->all(),
        ];
    }
}
