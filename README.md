[![Latest Version on Packagist](https://img.shields.io/packagist/v/veiliglanceren/laravel-seo-sitemap.svg?style=flat-square)](https://packagist.org/packages/veiliglanceren/laravel-seo-sitemap)
[![Total Downloads](https://img.shields.io/packagist/dt/veiliglanceren/laravel-seo-sitemap.svg?style=flat-square)](https://packagist.org/packages/veiliglanceren/laravel-seo-sitemap)
![Laravel Versions](https://img.shields.io/badge/Laravel-^10|^11|^12.*-blue)
![PHP Versions](https://img.shields.io/badge/PHP->_8.1-blue)

---

# Laravel SEO Sitemap

Want better Google rankings? Generating a clean and up-to-date sitemap is one of the easiest wins for your website’s SEO. With this package, your sitemap is always synced with your route and content structure, no manual edits needed. Search engines like Google and Bing use your sitemap to crawl your site smarter and faster, which means your new pages and updates show up in search results sooner. Whether you're running a blog, webshop, or custom platform, an automated sitemap gives you an edge in visibility and indexing accuracy.

**Lightweight. Extensible. Template-driven.**

## 🚀 Features of SEO Laravel Sitemap

- 🔍 Automatic sitemap generation from named routes via `->sitemap()`
- 🧩 Advanced route templates via `->sitemapUsing(MyTemplate::class)`
- 🧠 Built-in `Template` abstract with helpers like `urlsFromModel()`
- ✏️ Configure `lastmod`, `priority`, `changefreq` per URL
- 💾 Save or serve sitemaps via disk or route
- 🧪 Fully tested with Pest and Laravel Testbench
- 📦 Optional meta-tag injection in `<head>`
- ✅ Laravel 10, 11, and 12 support

## `📦` Installation of the Laravel sitemap package

This package is quick to set up and works out-of-the-box with Laravel 10, 11, and 12. After installing via Composer, you can instantly publish the sitemap route and configuration using a single command. The `php artisan sitemap:install` command automatically adds a new `sitemap.php` route file and wires it into your existing web.php, so your sitemap is live without extra setup. It’s the easiest way to boost your SEO visibility with structured sitemap data.

```bash
composer require veiliglanceren/laravel-seo-sitemap
```

Publish the route & config:

```bash
php artisan sitemap:install
php artisan vendor:publish --tag=sitemap-config
```

---

## `🧭` How to use the sitemap package

This package offers a clean and developer-friendly approach to sitemap generation in Laravel. Whether you're working with static pages or dynamic content from models, adding them to your sitemap is seamless. Use a single macro call for simple routes, or create powerful model-driven templates using the built-in abstract `Template` class to handle large, dynamic datasets. With just a few lines of code, your entire site structure becomes SEO-friendly and ready for search engine indexing.

### `✅` Static routes implemented in sitemap by 1 line in the routes/web.php file

The `Route` is getting implemented by calling the `->sitemap()` Macro.

```php
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

Route::get('/contact', ContactController::class)
    ->name('contact')
    ->sitemap()
    ->changefreq(ChangeFrequency::WEEKLY)
    ->priority('0.8');
```

#### Available `Route` Macros

The package includes expressive route macros that make it easy to configure sitemap settings directly in your `routes/web.php` file.

##### `->sitemap()`
Marks the route as sitemap-included.

```php
Route::get('/about', AboutController::class)
    ->name('about')
    ->sitemap();
```

##### `->changefreq(ChangeFrequency $frequency)`
Defines how frequently the content at the URL is likely to change.

```php
use VeiligLanceren\LaravelSeoSitemap\Support\Enums\ChangeFrequency;

Route::get('/blog', BlogController::class)
    ->name('blog.index')
    ->sitemap()
    ->changefreq(ChangeFrequency::WEEKLY);
```

##### `->priority(string $priority)`
Sets the priority of this URL relative to other URLs on your site.

```php
Route::get('/contact', ContactController::class)
    ->name('contact')
    ->sitemap()
    ->priority('0.8');
```

> 💡 These macros can be chained for fluent configuration and better readability.

### `🧩` Model-driven Template class for easy implementation in sitemap

Use a custom `Template` that extends the abstract `Template` class:

```php
// routes/web.php
Route::get('/blog/{slug}', BlogController::class)
    ->name('blog.show')
    ->sitemapUsing(\App\Sitemap\Templates\PostTemplate::class);
```

#### Example custom `Template` for implementing dynamic routes in sitemap

Read more about all of the helper functions: [template helper functions](docs/template-helper-functions.md)

```php
namespace App\Sitemap\Templates;

use App\Models\Post;
use Illuminate\Routing\Route;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Template;

class PostTemplate extends Template
{
    public function generate(Route $route): iterable
    {
        yield from $this->urlsFromModel(Post::class, $route, function (Post $post, Route $route) {
            return Url::make(route($route->getName(), ['slug' => $post->slug]))
                ->lastmod($post->updated_at)
                ->priority(0.6);
        });
    }
}
```

---

## `📂` Make an index for multiple sitemaps

Generate an index that references multiple sitemap files (e.g. per section):

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\SitemapIndex;

$sitemapIndex = SitemapIndex::make([
    'https://example.com/sitemap-pages.xml',
    'https://example.com/sitemap-posts.xml',
]);
```

You can dynamically add entries and pretty-print XML:

```php
$sitemapIndex->add('https://example.com/sitemap-products.xml');

Storage::disk('public')->put('sitemap.xml', $sitemapIndex->toXml());
```

Alternatively, mark routes with an index and let the CLI generate the index and files for you:

```php
Route::get('/blog', fn () => 'Blog')
    ->sitemapIndex('blog');

Route::get('/pages', fn () => 'Pages')
    ->sitemapIndex('pages');

// php artisan sitemap:generate
```

This will produce `sitemap-blog.xml`, `sitemap-pages.xml` and an `sitemap.xml` index linking to them.

📖 Read more: [docs/sitemapindex.md](docs/sitemapindex.md)

---

## `🧪` Generating sitemaps

```php
use VeiligLanceren\LaravelSeoSitemap\Facades\Sitemap;

Sitemap::fromRoutes()
    ->getSitemap()
    ->save('sitemap.xml', 'public');
```

Or use the CLI:

```bash
php artisan sitemap:generate
```

---

## `🖼` Add images to the sitemap 

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

$url = Url::make('https://example.com')
    ->addImage(Image::make('https://example.com/image1.jpg')->title('Hero 1'))
    ->addImage(Image::make('https://example.com/image2.jpg')->title('Hero 2'));
```

---

## `🔗` Meta tag helper

```blade
<head>
    {!! Sitemap::meta() !!}
</head>
```

Outputs:

```html
<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />
```

---

## `🧪` Testing

```bash
vendor/bin/pest
```

SQLite must be enabled for in-memory testing.

---

## 📄 License

MIT © [VeiligLanceren.nl](https://veiliglanceren.nl)
