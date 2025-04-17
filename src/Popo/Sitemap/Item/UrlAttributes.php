<?php

namespace VeiligLanceren\LaravelSeoSitemap\Popo\Sitemap\Item;

use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class UrlAttributes
{
    /**
     * @var string
     */
    public string $loc;

    /**
     * @var string|null
     */
    public ?string $lastmod = null;

    /**
     * @var ChangeFrequency|string|null
     */
    public ChangeFrequency|string|null $changefreq = null;

    /**
     * @var float|null
     */
    public ?float $priority = null;

    /**
     * @var array
     */
    public array $meta = [];

    /**
     * @param string $loc
     * @param string|null $lastmod
     * @param ChangeFrequency|string|null $changefreq
     * @param float|null $priority
     * @param array $meta
     */
    public function __construct(
        string $loc,
        ?string $lastmod = null,
        ChangeFrequency|string|null $changefreq = null,
        ?float $priority = null,
        array $meta = [],
    ) {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = $priority;
        $this->meta = $meta;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            $data['loc'],
            $data['lastmod'] ?? null,
            $data['changefreq'] ?? null,
            isset($data['priority']) ? (float) $data['priority'] : null,
            $data['meta'] ?? [],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
            'changefreq' => $this->changefreq instanceof ChangeFrequency
                ? $this->changefreq->value
                : $this->changefreq,
            'priority' => $this->priority,
            'meta' => $this->meta,
        ];
    }
}
