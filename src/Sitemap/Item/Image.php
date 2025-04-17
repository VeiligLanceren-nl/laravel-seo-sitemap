<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap\Item;

use SimpleXMLElement;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapItem;
use VeiligLanceren\LaravelSeoSitemap\Popo\Sitemap\Item\ImageAttributes;

class Image extends SitemapItem
{
    /**
     * @var string
     */
    protected string $loc;

    /**
     * @var string|null
     */
    protected ?string $caption = null;

    /**
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * @var string|null
     */
    protected ?string $license = null;

    /**
     * @var string|null
     */
    protected ?string $geo_location = null;

    /**
     * @param SimpleXMLElement $element
     * @return static
     */
    public static function fromXml(SimpleXMLElement $element): static
    {
        $image = $element->children('http://www.google.com/schemas/sitemap-image/1.1')->image;

        $item = new static();
        $item->loc = (string) $image->loc;
        $item->caption = isset($image->caption) ? (string) $image->caption : null;
        $item->title = isset($image->title) ? (string) $image->title : null;
        $item->license = isset($image->license) ? (string) $image->license : null;

        return $item;
    }

    /**
     * @param string $loc
     * @param ImageAttributes|array|null $attributes
     * @return static
     */
    public static function make(string $loc, ImageAttributes|array|null $attributes = null): static
    {
        $attributes = is_array($attributes)
            ? ImageAttributes::fromArray($attributes)
            : $attributes;

        $instance = new static();
        $instance->loc = $loc;
        $instance->caption = $attributes?->caption;
        $instance->title = $attributes?->title;
        $instance->license = $attributes?->license;
        $instance->geo_location = $attributes?->geo_location;

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
     * @param string $caption
     * @return $this
     */
    public function caption(string $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $license
     * @return $this
     */
    public function license(string $license): static
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @param string $geoLocation
     * @return $this
     */
    public function geoLocation(string $geoLocation): static
    {
        $this->geo_location = $geoLocation;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'loc' => $this->loc,
            'caption' => $this->caption,
            'title' => $this->title,
            'license' => $this->license,
            'geo_location' => $this->geo_location,
        ]);
    }
}
