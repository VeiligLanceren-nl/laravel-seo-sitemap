<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;

class Sitemap
{
    /**
     * @var Collection
     */
    protected Collection $items;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @var array
     */
    protected static array $providers = [];

    /**
     * Sitemap constructor.
     */
    public function __construct()
    {
        $this->items = collect();
    }

    /**
     * Create sitemap from routes.
     *
     * @return self
     */
    public static function fromRoutes(): self
    {
        $sitemap = new static();

        $sitemap->items = RouteSitemap::urls();

        return $sitemap;
    }

    /**
     * Register a sitemap provider class.
     *
     * @param string $provider
     * @return void
     */
    public static function registerProvider(string $provider): void
    {
        static::$providers[] = $provider;
    }

    /**
     * Create sitemap from registered providers.
     *
     * @return self
     */
    public static function fromProviders(): self
    {
        $sitemap = new static();

        foreach (static::$providers as $providerClass) {
            $provider = app($providerClass);

            if ($provider instanceof SitemapProviderInterface) {
                $sitemap->items = $sitemap->items->merge($provider->getUrls());
            }
        }

        return $sitemap;
    }

    /**
     * Merge another sitemap into this one.
     *
     * @param Sitemap $other
     * @return $this
     */
    public function merge(self $other): self
    {
        $this->items = $this->items->merge($other->items);

        return $this;
    }

    /**
     * Make a new sitemap instance.
     *
     * @param array $items
     * @param array $options
     * @return static
     */
    public static function make(array $items = [], array $options = []): static
    {
        $instance = new static();

        $instance->items = collect($items);
        $instance->options = $options;

        return $instance;
    }

    /**
     * Set the items.
     *
     * @param Collection $items
     * @return $this
     */
    public function items(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Set the options.
     *
     * @param array $options
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Save the sitemap to disk.
     *
     * @param string $path
     * @param string $disk
     * @return void
     */
    public function save(string $path, string $disk): void
    {
        $xml = XmlBuilder::build($this->items, $this->options);

        Storage::disk($disk)->put($path, $xml);
    }

    /**
     * Convert the sitemap to XML string.
     *
     * @return string
     */
    public function toXml(): string
    {
        return XmlBuilder::build($this->items, $this->options);
    }

    /**
     * Convert the sitemap to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'options' => $this->options,
            'items' => $this->items->map(fn (SitemapItem $item) => $item->toArray())->all(),
        ];
    }
}
