# 🧩 Template Helper Functions

The `Template` abstract class provides expressive, reusable helper methods for generating sitemap entries from your data sources.

---

## `urlsFromModel(...)`

**Generate URLs from any Eloquent model.**

### Signature
```php
public function urlsFromModel(
    string $modelClass,
    Route $route,
    callable $callback = null,
    Builder $query = null,
    bool $useCursor = true,
    ?int $chunkSize = null
): iterable
```

### Description
- **`modelClass`**: Eloquent model class (e.g. `Post::class`)
- **`route`**: The route object bound to this template
- **`callback`**: Customize the `Url` object per model
- **`query`**: Optional query override
- **`useCursor`**: Use cursor for memory-efficient iteration (default: true)
- **`chunkSize`**: Use chunking instead of cursor

### Default behavior
If no callback is provided:
```php
Url::make(route($route->getName(), $model))
```

### Example
```php
yield from $this->urlsFromModel(Post::class, $route, function (Post $post, Route $route) {
    return Url::make(route($route->getName(), ['slug' => $post->slug]))
        ->lastmod($post->updated_at)
        ->priority(0.6);
});
```

---

## `urlsFromIterable(...)`

**Generate URLs from any iterable (array, collection, etc.)**

### Signature
```php
public function urlsFromIterable(
    iterable $items,
    Route $route,
    callable $callback
): iterable
```

### Example
```php
$items = ['apple', 'banana', 'orange'];

yield from $this->urlsFromIterable($items, $route, function ($item, $route) {
    return Url::make(route($route->getName(), ['slug' => $item]));
});
```

---

## `singleUrl(...)`

**Manually define a single sitemap URL.**

### Signature
```php
public function singleUrl(string $url, callable $configure = null): Url
```

### Example
```php
yield $this->singleUrl('https://example.com/contact', fn (Url $url) =>
    $url->lastmod('2024-12-12')->priority(0.8)
);
```

---

## `paginatedUrls(...)`

**Generate paginated URLs like `/page/1`, `/page/2`, etc.**

### Signature
```php
public function paginatedUrls(
    Route $route,
    int $totalItems,
    int $perPage = 20,
    string $pageParam = 'page',
    array $extraParams = [],
    bool $skipPageOne = false
): iterable
```

### Example
```php
yield from $this->paginatedUrls($route, 145, 20, 'pagina', [], true);
```

Generates:
- `/pagina/2`
- `/pagina/3`
- ... (skipping page 1)

---

## ✅ Summary

| Method               | Purpose                                  | Use Case                            |
|----------------------|-------------------------------------------|--------------------------------------|
| `urlsFromModel()`    | From Eloquent models                      | Posts, products, categories          |
| `urlsFromIterable()` | From iterable data                        | Static arrays, API results           |
| `singleUrl()`        | Manually define 1 URL                     | Standalone entries                   |
| `paginatedUrls()`    | For paginated listing pages               | Blog archives, shops, search results |
