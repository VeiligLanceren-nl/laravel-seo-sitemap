<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap\Item;

use DateTimeInterface;
use SimpleXMLElement;
use VeiligLanceren\LaravelSeoSitemap\Popo\Sitemap\Item\UrlAttributes;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItem;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

class Url extends SitemapItem
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
     * @var string|null
     */
    public ?string $priority = null;

    /**
     * @var ChangeFrequency|null
     */
    public ?ChangeFrequency $changefreq = null;

    /**
     * @var Image[]
     */
    public array $images = [];

    /**
     * @var array<string, mixed>
     */
    public array $meta = [];

    /**
     * @param SimpleXMLElement $xml
     * @return static
     */
    public static function fromXml(SimpleXMLElement $xml): Url
    {
        $attributes = new UrlAttributes(
            loc: (string) $xml->loc,
            lastmod: (string) $xml->lastmod,
            changefreq: isset($xml->changefreq)
                ? ChangeFrequency::tryFrom((string) $xml->changefreq)
                : null,
            priority: isset($xml->priority)
                ? (float) $xml->priority
                : null,
        );

        $url = Url::make($attributes);

        if (isset($xml->meta)) {
            $meta = [];

            foreach ($xml->meta->children() as $key => $value) {
                $meta[$key] = (string) $value;
            }

            $url->meta = $meta;
        }

        return $url;
    }

    /**
     * @param string|UrlAttributes $loc
     * @param string|DateTimeInterface|null $lastmod
     * @param string|null $priority
     * @param ChangeFrequency|string|null $changeFrequency
     * @param UrlAttributes|array|null $attributes
     * @return static
     */
    public static function make(
        string|UrlAttributes $loc,
        string|DateTimeInterface $lastmod = null,
        string $priority = null,
        ChangeFrequency|string $changeFrequency = null,
        UrlAttributes|array|null $attributes = null,
    ): static {
        if ($loc instanceof UrlAttributes) {
            $attributes = $loc;
            $loc = $attributes->loc;
        }

        $attributes = is_array($attributes)
            ? UrlAttributes::fromArray($attributes)
            : $attributes;

        $instance = (new static())->loc($loc);

        if ($lastmod) {
            $instance->lastmod($lastmod);
        } elseif ($attributes?->lastmod) {
            $instance->lastmod($attributes->lastmod);
        }

        if ($priority) {
            $instance->priority($priority);
        } elseif ($attributes?->priority !== null) {
            $instance->priority((string) $attributes->priority);
        }

        if ($changeFrequency) {
            if (is_string($changeFrequency)) {
                $instance->changefreq(ChangeFrequency::from($changeFrequency));
            } else {
                $instance->changefreq($changeFrequency);
            }
        } elseif ($attributes?->changefreq) {
            if (is_string($changeFrequency)) {
                $instance->changefreq(ChangeFrequency::from($attributes->changefreq));
            } else {
                $instance->changefreq($attributes->changefreq);
            }
        }

        if ($attributes?->meta) {
            $instance->meta = $attributes->meta;
        }

        return $instance;
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
        $this->changefreq = $changefreq;

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
            'loc' => url($this->loc),
            'lastmod' => $this->lastmod,
            'priority' => $this->priority,
            'changefreq' => $this->changefreq,
        ]);

        if (!empty($this->images)) {
            $data['images'] = array_map(fn(Image $img) => $img->toArray(), $this->images);
        }

        return $data;
    }
}
