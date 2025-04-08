![Static Badge](https://img.shields.io/badge/Version-1.2.2-blue)
![Static Badge](https://img.shields.io/badge/Laravel-12.*-blue)
![Static Badge](https://img.shields.io/badge/PHP->_8.3-blue)

![Veilig Lanceren](/veilig-lanceren-logo.png)

This package is maintained by VeiligLanceren.nl, your partner in website development and everything else to power up your online company. More information available on [our website](https://veiliglanceren.nl).

# Laravel SEO Sitemap

A lightweight and extensible sitemap generator for Laravel that supports automatic route discovery, custom URL entries, and XML generation — designed for SEO optimization.

## 🚀 Features

- Generate sitemaps from named Laravel routes using a macro: `->sitemap()`
- Customize URLs with `lastmod`, `priority`, `changefreq`
- Clean XML output with optional pretty-printing
- Store sitemaps to disk
- Artisan command to update `lastmod` for routes
- Fully tested with Pest and Laravel Testbench
- Default `/sitemap.xml` route that serves the configured sitemap location

---

## 📦 Installation

```bash
composer require veiliglanceren/laravel-seo-sitemap
```

---

## ⚙️ Configuration

If used outside Laravel auto-discovery, register the service provider:

```php
// bootstrap/providers.php
return [
    VeiligLanceren\LaravelSeoSitemap\SitemapServiceProvider::class,
];
```

Publish the `config/sitemap.php` config file:

```bash
php artisan vendor:publish --tag=sitemap-config
```

Publish the migration (if using `lastmod` tracking):

```bash
php artisan vendor:publish --tag=sitemap-migration
php artisan migrate
```

---

## 🧭 Usage

- 📄 [Full Sitemap class documentation](docs/sitemap.md)
- 📄 [Url class documentation](docs/url.md)
- 📄 [Url image documentation](docs/image.md)
- 📄 [Sitemap Index documentation](docs/sitemapindex.md)

### Basic usage

```php
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact')                         // 🔖 Sets the route name
    ->sitemap()                               // ✅ Include in sitemap
    ->changefreq(ChangeFrequency::WEEKLY)     // ♻️  Update frequency: weekly
    ->priority('0.8');                        // ⭐ Priority for search engines
```

```php
$sitemap = Sitemap::fromRoutes();
$sitemap->save('sitemap.xml', 'public');
```

### Static usage

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

$url = Url::make('https://example.com')
    ->lastmod('2025-01-01')
    ->priority('0.8')
    ->changefreq(ChangeFrequency::WEEKLY);

$sitemap = Sitemap::make([$url]);
$sitemap->save('sitemap.xml', 'public');
```

---

### Sitemap index usage

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

$sitemapIndex = SitemapIndex::make([
    'https://example.com/sitemap-posts.xml',
    'https://example.com/sitemap-pages.xml',
]);

$sitemapIndex->toXml();
```

To save:

```php
Storage::disk('public')->put('sitemap.xml', $sitemapIndex->toXml());
```

### 🖼 Adding Images to URLs

You can attach one or more `<image:image>` elements to a `Url` entry:

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

$url = Url::make('https://example.com')
    ->addImage(Image::make('https://example.com/image1.jpg')->title('Hero 1'))
    ->addImage(Image::make('https://example.com/image2.jpg')->title('Hero 2'));
```

These images will be embedded under the `<url>` node in the generated XML:

```xml
<url>
  <loc>https://example.com</loc>
  <image:image>
    <image:loc>https://example.com/image1.jpg</image:loc>
    <image:title>Hero 1</image:title>
  </image:image>
  <image:image>
    <image:loc>https://example.com/image2.jpg</image:loc>
    <image:title>Hero 2</image:title>
  </image:image>
</url>
```

Each `Image` supports optional fields: `caption`, `title`, `license`, and `geo_location`.

## Change frequencies

The package is providing an enum with the possible change frequencies as documented on [sitemaps.org](https://www.sitemaps.org/protocol.html#changefreqdef).

### Available frequencies
- `ChangeFrequency::ALWAYS`
- `ChangeFrequency::HOURLY`
- `ChangeFrequency::DAILY`
- `ChangeFrequency::WEEKLY`
- `ChangeFrequency::MONTHLY`
- `ChangeFrequency::YEARLY`
- `ChangeFrequency::NEVER`


## 🛠 Update `lastmod` via Artisan

```bash
php artisan url:update contact
```

This updates the `lastmod` timestamp for the route `contact` using the current time.

## Sitemap meta helper

Add the Sitemap URL to your meta data with the helper provided by the package. By default it will use the default `/sitemap.xml` URL.

```php
<head>
    <title>Your title</title>
    {{ sitemap_meta_tag($customUrl = null) }}
</head>
```


---

## ✅ Testing

Run tests using Pest:

```bash
vendor/bin/pest
```

Make sure you have SQLite enabled for in-memory testing.

---

## 📂 Folder Structure

- `src/` - Core sitemap logic
- `tests/` - Pest feature & unit tests
- `database/migrations/` - `url_metadata` tracking support
- `routes/` - Uses Laravel route inspection
- `docs/` - Extended documentation

---

## 📄 License

MIT © Veilig Lanceren
