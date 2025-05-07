# 🧩 Template & Model‑Driven URLs

Automating large and dynamic sitemaps often means pulling thousands of URLs from the database.
`->sitemapUsing()` lets you plug **either** an Eloquent model **or** a small "template" class into the route definition. The package then asks that model / template for every possible URL and merges the result into your sitemap.

---

## ⚡ Quick start

### 1. Scaffold a template class (optional)

```bash
php artisan sitemap:template PostTemplate
```

### 2. Implement the template (app/Sitemap/ItemTemplates/PostTemplate.php)

```php
namespace App\SitemapTemplates;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use App\Models\Post;
use VeiligLanceren\LaravelSeoSitemap\Url;
use VeiligLanceren\LaravelSeoSitemap\Contracts\SitemapItemTemplate;

class PostTemplate implements SitemapItemTemplate
{
    /**
     * Turn every Post model into a <url> entry.
     *
     * @param  Route  $route  The Laravel Route instance for /blog/{slug}
     * @return iterable<Url>
     */
    public function generate(Route $route): iterable
    {
        return Post::published()
            ->cursor()
            ->map(fn (Post $post) =>
                Url::make(route($route->getName(), $post))
                    ->lastmod($post->updated_at)
                    ->priority(Post::isImportant($post) ? '0.9' : '0.5')
            );
    }

    /**
     * Allow foreach ($template as $url) {}
     */
    public function getIterator(): \Traversable
    {
        yield from $this->generate(app(Route::class));
    }
}
```

### 3. Wire the template to the route (routes/web.php)

```php
Route::get('/blog/{slug}', BlogController::class)
    ->name('blog.show')
    ->sitemapUsing(PostTemplate::class);
```

That’s it—`Sitemap::fromRoutes()` will now include **every** blog post.

---

## 🐘 Using an Eloquent Model directly

Too lazy for a template? Pass the model class itself—`all()` will be iterated.

```php
Route::get('/product/{product}', ProductController::class)
    ->name('product.show')
    ->sitemapUsing(App\Models\Product::class);
```

The package will call `Product::all()` and convert each model into an URL by simply passing the model instance to `route($name, $model)`.

---

## 🔍 How does it work?

1. **Route Macro** – `Route::sitemapUsing()` stores two route defaults: `sitemap` = `true` and `sitemap_generator` = the class you provided.
2. **Collection Stage** – `RouteSitemap::urls()` detects the `sitemap_generator` default and instantiates it.
3. **Generation** – If the class **implements** `\IteratorAggregate`, its `getIterator()` is used. Otherwise the package calls a `generate(Route $route)` method directly.
4. **Url Objects** – Every item returned must be (or castable to) a `VeiligLanceren\LaravelSeoSitemap\Url` instance.

---

## 🤖 Tips & Best practices

| Scenario                    | Tip                                                                                                   |
| --------------------------- | ----------------------------------------------------------------------------------------------------- |
| Massive tables              | Use `->cursor()` instead of `->get()` to avoid loading everything into memory.                        |
| Frequent updates            | Store `updated_at` on the model and set it via `->lastmod()` to help search engines re‑crawl smartly. |
| Multilingual routes         | Loop over every locale and call `Url::make()` multiple times for the same model.                      |
| Accessing the current route | The `Route` object is injected so you can safely reference placeholders and route name.               |
| Testing                     | Templates are plain PHP—unit‑test the `generate()` method just like any other class.                  |

---

Need more examples? Check the **tests** folder or open an issue 🕷️
