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

---

## 📦 Installation

```bash
composer require veiliglanceren/laravel-seo-sitemap
```

---

## ⚙️ Configuration

If used outside Laravel auto-discovery, register the service provider:

```php
// config/app.php
'providers' => [
    VeiligLanceren\LaravelSeoSitemap\SitemapServiceProvider::class,
],
```

Publish the migration (if using `lastmod` tracking):

```bash
php artisan migrate
```

---

## 🧭 Usage

### 📄 [Full Sitemap class documentation](docs/sitemap.md)
### 📄 [Full Url class documentation](docs/url.md)

#### Basic usage

```php
Route::get('/contact', fn () => view('contact'))
    ->name('contact')
    ->sitemap() // 👈 sets sitemap = true
    ->priority('0.8'); // 👈 sets priority = 0.8
```

```php
$sitemap = Sitemap::fromRoutes();
$sitemap->save('sitemap.xml', 'public');
```

```php
use VeiligLanceren\LaravelSeoSitemap\Url;
use VeiligLanceren\LaravelSeoSitemap\Enums\ChangeFrequency;

Url::make('https://example.com')
    ->lastmod('2025-01-01')
    ->priority('0.8')
    ->changefreq(ChangeFrequency::WEEKLY);
```

---

## 🛠 Update `lastmod` via Artisan

```bash
php artisan url:update contact
```

This updates the `lastmod` timestamp for the route `contact` using the current time.

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
