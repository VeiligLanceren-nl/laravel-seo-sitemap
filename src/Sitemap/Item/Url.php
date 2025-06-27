<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap\Item;

use DateTimeInterface;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItem;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class Url extends SitemapItem
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
     * @var Image[]
     */
    protected array $images = [];

    /**
     * @param string $loc
     * @param string|DateTimeInterface|null $lastmod
     * @param string|null $priority
     * @param ChangeFrequency|null $changeFrequency
     * @return static
     */
    public static function make(
        string $loc,
        string|DateTimeInterface $lastmod = null,
        string $priority = null,
        ChangeFrequency $changeFrequency = null,
    ): static {
        $sitemap = (new static())->loc($loc);

        if ($lastmod) {
            $sitemap->lastmod($lastmod);
        }

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
     * @param Image $image
     * @return $this
     */
    public function addImage(Image $image): static
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * @param array $images
     * @return $this
     */
    public function images(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = array_filter([
            'loc' => $this->loc,
            'lastmod' => $this->lastmod,
            'priority' => $this->priority,
            'changefreq' => $this->changefreq,
        ]);

        if (!empty($this->images)) {
            $data['images'] = array_map(fn(Image $img) => $img->toArray(), $this->images);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }
}
