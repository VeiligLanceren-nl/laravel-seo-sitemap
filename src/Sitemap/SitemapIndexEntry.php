<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use DateTimeInterface;

class SitemapIndexEntry
{
    protected string $loc;
    protected ?string $lastmod = null;

    public function __construct(string $loc, DateTimeInterface|string|null $lastmod = null)
    {
        $this->loc = $loc;
        if ($lastmod) {
            $this->lastmod = $lastmod instanceof DateTimeInterface
                ? $lastmod->format('Y-m-d')
                : $lastmod;
        }
    }

    public static function make(string $loc, DateTimeInterface|string|null $lastmod = null): static
    {
        return new static($loc, $lastmod);
    }

    public function getLoc(): string
    {
        return $this->loc;
    }

    public function getLastmod(): ?string
    {
        return $this->lastmod;
    }

    public function toArray(): array
    {
        return array_filter([
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
        ]);
    }
}
