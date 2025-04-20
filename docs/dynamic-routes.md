# Dynamic Routes for Sitemap

The `->dynamic()` macro allows you to register routes that generate dynamic URL entries for the sitemap, using parameter combinations fetched at runtime.

## 🚀 Usage

Register a dynamic route with parameter sets:

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

You can also fetch dynamic parameters from the database:

```php
->dynamic(fn () => new StaticDynamicRoute(
    \App\Models\Post::all()->map(fn ($post) => DynamicRouteChild::make(['slug' => $post->slug]))
))
```

## 📄 Output

This will generate the following URLs in your sitemap:

- `/blog/first-post`
- `/blog/second-post`

## 🛠 Advanced

You can implement your own `DynamicRoute` subclass if you want to customize behavior beyond `StaticDynamicRoute`.

---

Enjoy automatic sitemap entries from your dynamic content! 🎉
