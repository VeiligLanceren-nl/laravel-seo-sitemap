<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Traversable;
use ArrayIterator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\SitemapTooLargeException;
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
     * @var int|null
     */
    protected ?int $maxItems = 500;

    /**
     * @var bool
     */
    protected bool $throwOnLimit = true;

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
     * @throws SitemapTooLargeException
     */
    public static function fromProviders(): self
    {
        $sitemap = new static();

        foreach (static::$providers as $providerClass) {
            $provider = app($providerClass);

            if ($provider instanceof SitemapProviderInterface) {
                $sitemap->addMany($provider->getUrls());
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
     * @throws SitemapTooLargeException
     */
    public function items(Collection $items): static
    {
        $this->items = collect();
        $this->addMany($items);

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
     * @param int|null $maxItems
     * @param bool $throw
     * @return $this
     */
    public function enforceLimit(?int $maxItems = 500, bool $throw = true): static
    {
        $this->maxItems = $maxItems;
        $this->throwOnLimit = $throw;

        return $this;
    }

    /**
     * @return $this
     */
    public function bypassLimit(): static
    {
        return $this->enforceLimit($this->maxItems, false);
    }

    /**
     * @param SitemapItem $item
     * @return void
     * @throws SitemapTooLargeException
     */
    public function add(SitemapItem $item): void
    {
        $this->guardMaxItems(1);
        $this->items->push($item);
    }

    /**
     * @param iterable $items
     * @return void
     * @throws SitemapTooLargeException
     */
    public function addMany(iterable $items): void
    {
        if (! is_countable($items) && $items instanceof Traversable) {
            $items = iterator_to_array($items);
        }

        $count = is_countable($items)
            ? count($items)
            : iterator_count($items instanceof Traversable ? $items : new ArrayIterator($items));

        $this->guardMaxItems($count);

        foreach ($items as $item) {
            $this->items->push($item);
        }
    }

    /**
     * @param int $adding
     * @return void
     * @throws SitemapTooLargeException
     */
    protected function guardMaxItems(int $adding): void
    {
        if (! $this->throwOnLimit || $this->maxItems === null) {
            return;
        }

        if ($this->items->count() + $adding > $this->maxItems) {
            throw new SitemapTooLargeException($this->maxItems);
        }
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
