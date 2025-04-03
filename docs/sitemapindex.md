# 📄 Sitemap Index

The `SitemapIndex` class generates an [XML Sitemap Index](https://www.sitemaps.org/protocol.html#index) file that references multiple individual sitemap files.

---

## ✅ Features

- Add multiple sitemap URLs
- Export to XML and array
- Supports pretty-printing
- Fully testable

---

## 🧱 Class: `SitemapIndex`

### 🔨 `SitemapIndex::make(array $locations = [], array $options = []): static`
Creates a new sitemap index instance.

```php
SitemapIndex::make([
    'https://example.com/sitemap-posts.xml',
    'https://example.com/sitemap-pages.xml',
], ['pretty' => true]);
```

### ➕ `add(string $loc): static`
Adds a single sitemap location.

```php
$index->add('https://example.com/sitemap-images.xml');
```

### 🔁 `toArray(): array`
Returns the sitemap index as an array:

```php
[
    'options' => [],
    'sitemaps' => [
        'https://example.com/sitemap-posts.xml',
        'https://example.com/sitemap-pages.xml',
    ]
]
```

### 🧾 `toXml(): string`
Returns a valid `sitemapindex` XML document.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>https://example.com/sitemap-posts.xml</loc>
    </sitemap>
    <sitemap>
        <loc>https://example.com/sitemap-pages.xml</loc>
    </sitemap>
</sitemapindex>
```

---

## 💾 Save to Disk

```php
Storage::disk('public')->put('sitemap.xml', $sitemapIndex->toXml());
```

---

## 🧪 Testing

See `SitemapIndexTest` for examples of:
- Creating the index
- Asserting the XML contents
- Saving and verifying with Laravel's filesystem

---

## 💡 Tip: Combine with Scheduled Jobs

You can use `SitemapIndex` alongside `Sitemap::make()` to generate individual files, then collect them into one index:

```php
$sitemapIndex = SitemapIndex::make();

foreach ($sections as $section) {
    Sitemap::make($section->urls())->save("sitemap-{$section->slug}.xml", 'public');

    $sitemapIndex->add(URL::to("/storage/sitemap-{$section->slug}.xml"));
}

$sitemapIndex->toXml();
```

---

## 📚 References
- [Sitemaps.org – Sitemap index](https://www.sitemaps.org/protocol.html#index)