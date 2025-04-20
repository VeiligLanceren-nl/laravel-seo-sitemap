<?php

namespace VeiligLanceren\LaravelSeoSitemap\Sitemap;

use Countable;
use Traversable;
use ArrayIterator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Exceptions\SitemapTooLargeException;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;

class Sitemap
{
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
    public function __construct(
        public ?array     $items = [],
        public ?string $path = null,
    ) {}

    /**
     * Create sitemap from routes.
     *
     * @return self
     */
    public static function fromRoutes(): self
    {
        $sitemap = new static(RouteSitemap::urls()->toArray());

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
     * @param string $disk
     * @param string $path
     * @return self|null
     */
    public static function fromStorage(string $disk, string $path): ?self
    {
        $content = Storage::disk($disk)->get($path);
        $content = ltrim($content); // defensive

        try {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($content);
            if (! $xml) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        $items = collect($xml->url)->map(function ($url) {
            if (isset($url->image)) {
                return Image::fromXml($url);
            }

            return Url::fromXml($url);
        });

        return new self(items: $items->all(), path: $path);
    }




    /**
     * Load all sitemap XML files and return a collection of Sitemap objects.
     *
     * @return Collection<Sitemap>
     */
    public static function load(): Collection
    {
        $disk = config('sitemap.disk', 'public');
        $directory = config('sitemap.directory', 'sitemaps');

        $files = Storage::disk($disk)->files($directory);

        return collect($files)
            ->filter(fn ($file) => Str::endsWith($file, '.xml'))
            ->map(fn ($path) => self::fromStorage($disk, $path))
            ->filter();
    }

    /**
     * Load sitemap from an XML file.
     *
     * @param string $path
     * @return static|null
     */
    public static function fromFile(string $path): ?self
    {
        $xml = simplexml_load_file($path);

        if (! $xml) {
            return null;
        }

        $items = collect($xml->url)->map(function ($url) {
            if (isset($url->image)) {
                return Image::fromXml($url);
            }

            return Url::fromXml($url);
        });

        return new self(items: $items, path: $path);
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

        $instance->items = $items;
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
        $this->items = [];
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
        $this->items[] = $item;
    }

    /**
     * @param Countable $items
     * @return void
     * @throws SitemapTooLargeException
     */
    public function addMany(Countable $items): void
    {
        $count = is_countable($items)
            ? count($items)
            : iterator_count(
                $items instanceof Traversable
                    ? $items
                    : new ArrayIterator($items)
            );
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

        if (count($this->items) + $adding > $this->maxItems) {
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
        $xml = XmlBuilder::build(Collection::make($this->items), $this->options);

        Storage::disk($disk)->put($path, $xml);
    }

    /**
     * Convert the sitemap to XML string.
     *
     * @return string
     */
    public function toXml(): string
    {
        return XmlBuilder::build(collect($this->items), $this->options);
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
            'items' => collect($this->items)->map(fn (SitemapItem $item) => $item->toArray())->all(),
        ];
    }
}
