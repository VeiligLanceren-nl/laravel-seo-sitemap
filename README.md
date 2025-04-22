![Static Badge](https://img.shields.io/badge/Version-1.3.3-blue)
![Static Badge](https://img.shields.io/badge/Laravel-12.*-blue)
![Static Badge](https://img.shields.io/badge/PHP->_8.3-blue)

![Veilig Lanceren](/veilig-lanceren-logo.png)

This package is maintained by [VeiligLanceren.nl](https://veiliglanceren.nl), your partner in website development and everything else to power up your online company.

# Laravel SEO Sitemap

A lightweight and extensible sitemap generator for Laravel that supports automatic route discovery, dynamic and static URL entries, and XML generation — designed for SEO optimization.

---

## 🚀 Features

- 🔍 Automatic sitemap generation from named routes via `->sitemap()` macro
- 📦 Dynamic route support via `->dynamic()` macro
- ✏️ Customize entries with `lastmod`, `priority`, `changefreq`
- 🧼 Clean and compliant XML output
- 💾 Store sitemaps to disk or serve via route
- 🛠 Artisan command for `lastmod` updates
- ✅ Fully tested using Pest and Laravel Testbench
- 🌐 Default `/sitemap.xml` route included

---

## 📦 Installation

```bash
composer require veiliglanceren/laravel-seo-sitemap
```

---

## ⚙️ Configuration

If you're not using Laravel package auto-discovery, register the provider manually:

```php
// bootstrap/providers.php
return [
    VeiligLanceren\LaravelSeoSitemap\SitemapServiceProvider::class,
];
```

Then publish the config file:

```bash
php artisan vendor:publish --tag=sitemap-config
```

And optionally publish & run the migration:

```bash
php artisan vendor:publish --tag=sitemap-migration
php artisan migrate
```

---

## 🧭 Usage

### 📄 Static Route Example

```php
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

Route::get('/contact', [ContactController::class, 'index'])
    ->name('contact')
    ->sitemap()
    ->changefreq(ChangeFrequency::WEEKLY)
    ->priority('0.8');
```

### 🔄 Dynamic Route Example

```php
use VeiligLanceren\Sitemap\Dynamic\StaticDynamicRoute;
use VeiligLanceren\Sitemap\Dynamic\DynamicRouteChild;

Route::get('/blog/{slug}', BlogController::class)
    ->name('blog.show')
    ->dynamic(fn () => new StaticDynamicRoute([
        DynamicRouteChild::make(['slug' => 'first-post']),
        DynamicRouteChild::make(['slug' => 'second-post']),
    ]));
```

### Generate Sitemap from Routes

```bash
php artisan sitemap:generate
```

Or via code:

```php
$sitemap = Sitemap::fromRoutes();
$sitemap->save('sitemap.xml', 'public');
```

---

## 🖼 Add Images to URLs

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

$url = Url::make('https://example.com')
    ->addImage(Image::make('https://example.com/image1.jpg')->title('Hero 1'))
    ->addImage(Image::make('https://example.com/image2.jpg')->title('Hero 2'));
```

---

## 🗂 Sitemap Index Support

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

$sitemapIndex = SitemapIndex::make([
    'https://example.com/sitemap-posts.xml',
    'https://example.com/sitemap-pages.xml',
]);

Storage::disk('public')->put('sitemap.xml', $sitemapIndex->toXml());
```

---

## 🔁 Change Frequencies

Use `ChangeFrequency` enum values:
- `ALWAYS`
- `HOURLY`
- `DAILY`
- `WEEKLY`
- `MONTHLY`
- `YEARLY`
- `NEVER`

```php
->changefreq(ChangeFrequency::WEEKLY)
```

---

## 🛠 Update lastmod

```bash
php artisan url:update contact
```

This sets the `lastmod` for the route to the current timestamp.

---

## 🔗 Meta Tag Helper

```blade
<head>
    {{ sitemap_meta_tag() }}
</head>
```

Outputs:

```html
<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />
```

---

## 🧪 Testing

```bash
vendor/bin/pest
```

SQLite must be enabled for in-memory testing.

---

## 📚 Documentation

- [`docs/sitemap.md`](docs/sitemap.md)
- [`docs/url.md`](docs/url.md)
- [`docs/image.md`](docs/image.md)
- [`docs/sitemapindex.md`](docs/sitemapindex.md)
- [`docs/dynamic-routes.md`](docs/dynamic-routes.md)

---

## 📂 Folder Structure

- `src/` – Core sitemap logic
- `tests/` – Pest feature & unit tests
- `docs/` – Documentation
- `routes/` – Laravel route macros
- `database/` – Optional migrations

---

## 📄 License

MIT © [VeiligLanceren.nl](https://veiliglanceren.nl)
