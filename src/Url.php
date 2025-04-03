<?php

namespace VeiligLanceren\LaravelSeoSitemap;

use DateTimeInterface;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class Url
{
    /**
     * @var string
     */
    protected string $loc;

    /**
     * @var string|null
     */
    protected ?string $lastmod = null;

    /**
     * @var string|null
     */
    protected ?string $priority = null;

    /**
     * @var string|null
     */
    protected ?string $changefreq = null;

    /**
     * @param string $loc
     * @param string|null $priority
     * @param ChangeFrequency|null $changeFrequency
     * @return static
     */
    public static function make(
        string $loc,
        string $priority = null,
        ChangeFrequency $changeFrequency = null,
    ): static
    {
        $sitemap = (new static())->loc($loc);

        if ($priority) {
            $sitemap->priority($priority);
        }

        if ($changeFrequency) {
            $sitemap->changefreq($changeFrequency);
        }

        return $sitemap;
    }

    /**
     * @param string $loc
     * @return $this
     */
    public function loc(string $loc): static
    {
        $this->loc = $loc;
        return $this;
    }

    /**
     * @param string|DateTimeInterface $lastmod
     * @return $this
     */
    public function lastmod(string|DateTimeInterface $lastmod): static
    {
        $this->lastmod = $lastmod instanceof DateTimeInterface
            ? $lastmod->format('Y-m-d')
            : $lastmod;

        return $this;
    }

    /**
     * @param string $priority
     * @return $this
     */
    public function priority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @param ChangeFrequency $changefreq
     * @return $this
     */
    public function changefreq(ChangeFrequency $changefreq): static
    {
        $this->changefreq = $changefreq->value;

        return $this;
    }

    /**
     * @return array<string, ?string>
     */
    public function toArray(): array
    {
        return array_filter([
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
            'priority' => $this->priority,
            'changefreq' => $this->changefreq,
        ]);
    }
}
