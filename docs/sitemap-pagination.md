# Traits

## `HasPaginatedSitemap`

The `HasPaginatedSitemap` trait allows you to easily generate paginated URLs for resource index pages, such as blog or news listing pages, in your sitemap templates.

This is particularly useful when you want to include URLs like `/blog?page=1`, `/blog?page=2`, etc., in your sitemap for paginated resource listings.

---

### Usage

1. **Include the trait in your sitemap template class:**

```php
use VeiligLanceren\LaravelSeoSitemap\Support\Traits\HasPaginatedSitemap;

class BlogIndexTemplate implements SitemapItemTemplate
{
    use HasPaginatedSitemap;

    public function generate(Route $route): iterable
    {
        $totalItems = Post::published()->count();
        $perPage = 20;

        yield from $this->paginatedUrls($route, $totalItems, $perPage);
    }
}
```

2. **Method Signature**

```php
protected function paginatedUrls(
    Route $route,
    int $totalItems,
    int $perPage = 20,
    string $pageParam = 'page',
    array $extraParams = [],
    bool $skipPageOne = false
): \Traversable
```

- **$route**: The current route instance.
- **$totalItems**: The total number of items in your resource (e.g., total blog posts).
- **$perPage**: The number of items displayed per page.
- **$pageParam**: The query parameter used for pagination (default: `'page'`).
- **$extraParams**: (Optional) Any additional route parameters to be merged.
- **$skipPageOne**: (Optional) If set to `true`, the first page (`?page=1`) is not included in the generated URLs.

---

### Example: Skipping Page 1

If your application routes `/blog` (without `?page=1`) to the first page, you may want to exclude the `?page=1` URL from the sitemap:

```php
yield from $this->paginatedUrls($route, $totalItems, $perPage, 'page', [], true);
```

---

### Example: Using Extra Route Parameters

If your paginated route requires extra parameters (e.g., category), provide them as an associative array:

```php
yield from $this->paginatedUrls($route, $totalItems, $perPage, 'page', ['category' => $category->slug]);
```

---

### Output

Each call to `paginatedUrls()` yields a `Url` object for each paginated page, which can be used directly in your sitemap template's `generate()` method.

---

### Notes

- This trait is useful for efficiently generating sitemap entries for paginated listings.
- For individual resource entries (e.g., `/blog/my-post`), use your own logic.
- Ensure your route/controller supports the pagination query parameter.

---

## See also

- [`SitemapItemTemplate` documentation](./template.md)
- [Laravel Pagination Documentation](https://laravel.com/docs/pagination)