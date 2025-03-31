<?php

namespace VeiligLanceren\LaravelSeoSitemap;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use VeiligLanceren\LaravelSeoSitemap\Macros\RouteSitemap;
use VeiligLanceren\LaravelSeoSitemap\Interfaces\SitemapProviderInterface;

class Sitemap
{
    /**
     * @var Collection
     */
    protected Collection $urls;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @var array
     */
    protected static array $providers = [];

    public function __construct()
    {
        $this->urls = collect();
    }

    /**
     * @return self
     */
    public static function fromRoutes(): self
    {
        $sitemap = new static();
        $sitemap->urls = RouteSitemap::urls();

        return $sitemap;
    }

    /**
     * @param string $provider
     * @return void
     */
    public static function registerProvider(string $provider): void
    {
        static::$providers[] = $provider;
    }

    /**
     * @return self
     */
    public static function fromProviders(): self
    {
        $sitemap = new static();

        foreach (static::$providers as $providerClass) {
            $provider = app($providerClass);

            if ($provider instanceof SitemapProviderInterface) {
                $sitemap->urls = $sitemap->urls->merge($provider->getUrls());
            }
        }

        return $sitemap;
    }

    /**
     * @param Sitemap $other
     * @return $this
     */
    public function merge(self $other): self
    {
        $this->urls = $this->urls->merge($other->urls);
        return $this;
    }

    /**
     * @param array $urls
     * @param array $options
     * @return Sitemap
     */
    public static function make(array $urls = [], array $options = []): static
    {
        $instance = new static();
        $instance->urls = collect($urls);
        $instance->options = $options;

        return $instance;
    }

    /**
     * @param Collection $urls
     * @return $this
     */
    public function urls(Collection $urls): static
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $path
     * @param string $disk
     * @return void
     */
    public function save(string $path, string $disk): void
    {
        $xml = XmlBuilder::build($this->urls, $this->options);
        Storage::disk($disk)->put($path, $xml);
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        return XmlBuilder::build($this->urls, $this->options);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'options' => $this->options,
            'urls' => $this->urls->map(fn (Url $url) => $url->toArray())->all(),
        ];
    }
}