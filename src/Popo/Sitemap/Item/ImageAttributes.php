<?php

namespace VeiligLanceren\LaravelSeoSitemap\Popo\Sitemap\Item;

use Scrumble\Popo\BasePopo;

class ImageAttributes extends BasePopo
{
    /**
     * @var string|null
     */
    public ?string $caption = null;

    /**
     * @var string|null
     */
    public ?string $title = null;

    /**
     * @var string|null
     */
    public ?string $license = null;

    /**
     * @var string|null
     */
    public ?string $geo_location = null;

    /**
     * @var array
     */
    public array $meta = [];

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            caption: $data['caption'] ?? null,
            title: $data['title'] ?? null,
            license: $data['license'] ?? null,
            geo_location: $data['geo_location'] ?? null,
            meta: $data['meta'] ?? [],
        );
    }

    /**
     * @param string|null $caption
     * @param string|null $title
     * @param string|null $license
     * @param string|null $geo_location
     * @param array $meta
     */
    public function __construct(
        ?string $caption = null,
        ?string $title = null,
        ?string $license = null,
        ?string $geo_location = null,
        array $meta = [],
    ) {
        $this->caption = $caption;
        $this->title = $title;
        $this->license = $license;
        $this->geo_location = $geo_location;
        $this->meta = $meta;
    }
}