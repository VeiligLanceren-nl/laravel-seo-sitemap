# 🖼 Image Support in Sitemap URLs

The `Image` class allows you to embed `<image:image>` tags in sitemap entries, helping search engines discover visual content on your pages.

---

## ✅ Features

- Associate one or more images with a URL
- Include optional metadata such as title, caption, geo location, and license
- Fully supported in XML generation

---

## 📦 Usage Example

```php
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Url;
use VeiligLanceren\LaravelSeoSitemap\Sitemap\Item\Image;

$url = Url::make('https://example.com')
    ->addImage(Image::make('https://example.com/image1.jpg')->title('Hero 1'))
    ->addImage(Image::make('https://example.com/image2.jpg')->caption('Scene 2'));
```

---

## 🧾 XML Output

```xml
<url>
  <loc>https://example.com</loc>
  <image:image>
    <image:loc>https://example.com/image1.jpg</image:loc>
    <image:title>Hero 1</image:title>
  </image:image>
  <image:image>
    <image:loc>https://example.com/image2.jpg</image:loc>
    <image:caption>Scene 2</image:caption>
  </image:image>
</url>
```

---

## 🛠 Available Fields

| Method         | Description                      |
|----------------|----------------------------------|
| `loc()`        | Image URL (required)             |
| `title()`      | Image title                      |
| `caption()`    | Image caption                    |
| `license()`    | License URL                      |
| `geoLocation()`| Geographic location of the image |

---

## ✅ Tip

Use descriptive titles and captions for better SEO and accessibility.