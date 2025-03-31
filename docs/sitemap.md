# Sitemap

The `Sitemap` class is the main entry point for generating sitemaps from either route metadata or manually provided URLs.

## Create a Sitemap manually

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap;
use VeiligLanceren\LaravelSeoSitemap\Url;

$sitemap = Sitemap::make([
    Url::make('https://example.com')
        ->lastmod('2024-01-01')
        ->priority('0.8')
        ->changefreq(ChangeFrequency::WEEKLY),
]);
```

## From registered routes

```php
$sitemap = Sitemap::fromRoutes();
```

This will scan all routes with `->defaults('sitemap', true)` (usually via `->sitemap()` macro).

## Save to disk

```php
$sitemap->save('sitemap.xml', 'public');
```

## Export to XML

```php
$xml = $sitemap->toXml();
```

Supports a `pretty` option for formatted XML:

```php
Sitemap::make([...], ['pretty' => true]);
```

## Array structure

```php
$sitemap->toArray();
```

Returns:

```php
[
  'options' => [...],
  'urls' => [...],
]
```
