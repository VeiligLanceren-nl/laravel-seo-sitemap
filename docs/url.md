# Url

The `Url` class represents a single URL entry in the sitemap. It supports all optional sitemap fields.

## Create a URL

```php
$url = Url::make('https://example.com');
```

## Set optional properties

```php
$url->lastmod('2024-01-01');
$url->priority('0.8');
$url->changefreq(ChangeFrequency::MONTHLY);
```

You can also use a fluent interface:

```php
Url::make('https://example.com')
    ->lastmod(now())
    ->priority('1.0')
    ->changefreq(ChangeFrequency::DAILY);
```

## Convert to array

```php
$url->toArray();
```

Returns only non-null fields, e.g.:

```php
[
  'loc' => 'https://example.com',
  'lastmod' => '2024-01-01',
  'priority' => '0.8',
  'changefreq' => 'daily'
]
```
