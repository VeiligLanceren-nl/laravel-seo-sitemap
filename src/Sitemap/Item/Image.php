<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap\Item;

class Image
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
     * @param string $loc
     * @return static
     */
    public static function make(string $loc): static
    {
        return (new static())->loc($loc);
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
