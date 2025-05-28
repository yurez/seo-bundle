# Gurtok SeoBundle

Modern and flexible SEO Bundle for Symfony 6/7 projects.
Easily manage meta tags, OpenGraph, Twitter cards, canonical URLs, hreflangs, and verification tags.

---

## 📦 Installation

Require the bundle via Composer:

```bash
composer require gurtok/seo-bundle
```

If you're using Symfony Flex, the bundle will be registered automatically.
Otherwise, manually add to `config/bundles.php`:

```php
return [
    Gurtok\SeoBundle\GurtokSeoBundle::class => ['all' => true],
];
```

---

## ⚙️ Configuration

Create a config file `config/packages/gurtok_seo.yaml`:

```yaml
gurtok_seo:
    allow_custom_meta: false         # Allow setting custom meta tags (true/false)
    auto_inject_response: true       # Auto-insert SEO meta into <head> if not manually called
    excluded_paths:
        - '/admin'
        - '/api'
    canonical_excluded_query_keys:
        - 'utm_source'
        - 'utm_medium'
        - 'utm_campaign'
        - 'utm_term'
        - 'utm_content'
        - 'ref'
        - 'fbclid'
        - 'page'
    defaults:
        title: { en: 'Default Title', uk: 'Типовий заголовок' } # Default title for the site
        title_separator: ' | '
        description: { en: 'Default description', uk: 'Типовий опис' }
        auto_canonical: true         # Automatically generate canonical URL
        meta:
            robots: 'index, follow'
        og:
            type: 'website'
        twitter:
            card: 'summary_large_image'
        verifications:
            google-site-verification: 'abc123'
        no_index: false              # Set to true if the site should not be indexed by search engines
        is_adult_content: false      # Set to true if the content is adult-oriented and will be marked as such
```

**Note:**
The default locale is automatically taken from `%kernel.default_locale%` (usually configured in `framework.yaml`).

Example `framework.yaml`:

```yaml
framework:
    default_locale: 'en'
```

---

## 🚀 Usage

### Using the Attribute on Controllers

You can define SEO metadata directly via PHP 8+ attributes:

```php
use Gurtok\SeoBundle\Attribute\SeoMeta;

#[SeoMeta(
    title: 'Homepage',
    titlePrefix: 'MySite',
    titleSeparator: ' ~ ',
    description: 'Welcome to our amazing website!',
    canonical: 'https://example.com',
    meta: ['robots' => 'index, follow'],
    og: [
        'title' => 'Homepage OG',
        'image' => 'https://example.com/image.jpg',
    ],
    twitter: ['card' => 'summary_large_image'],
    verifications: ['google-site-verification' => 'your-verification-code'],
    hreflangs: [
        'en' => 'https://example.com',
        'uk' => 'https://example.com/uk'
    ],
    noIndex: false,
    isAdultContent: false,
    disableDefaults: false
)]
public function __invoke()
{
    // ...
}
```

You can place the attribute either **on the controller method** or **on the class** — method attribute has priority.

---

### Localized Titles and Descriptions

You can specify translations via arrays:

```php
#[SeoMeta(
    title: [
        'en' => 'Homepage',
        'uk' => 'Головна сторінка'
    ],
    description: [
        'en' => 'Welcome!',
        'uk' => 'Ласкаво просимо!'
    ]
)]
```

The bundle automatically detects the current request locale (`RequestStack`) and uses the localized value.
Fallback is the default locale from `%kernel.default_locale%`, or the first available value.

---

## 🎨 Twig integration

In your Twig layout (`base.html.twig`):

```twig
<head>
    {{ seo() }}
</head>
```

This will render:

* `<title>` tag
* `<meta>` description
* `<meta>` robots
* OpenGraph tags
* Twitter card tags
* Canonical link
* Hreflang alternate links
* Verification meta tags

---

### 🔍 Twig Functions

| Twig Function        | Description                                            |
| -------------------- | ------------------------------------------------------ |
| `seo()`              | Renders everything (title + meta + OG + Twitter + etc) |
| `seo_title()`        | Renders only `<title>` tag                             |
| `seo_meta()`         | Renders only `<meta>` tags                             |
| `seo_og()`           | Renders OpenGraph tags                                 |
| `seo_open_graph()`   | Alias for `seo_og()`                                   |
| `seo_twitter()`      | Renders Twitter card tags                              |
| `seo_hreflangs()`    | Renders hreflang links                                 |
| `seo_verification()` | Renders verification tags                              |

---

### ⚙️ Twig Function Options

Functions `seo()` and `seo_meta()` accept an optional options array:

```twig
{{ seo({ include_title: true, skip_empty: true }) }}
```

* `include_title` (bool, default `true`) — whether to include the `<title>` tag.
* `skip_empty` (bool, default `true`) — skip rendering empty meta fields.

---

## 🔥 Auto Injection (Optional)

If you forget to call `{{ seo() }}` manually in Twig,
and `auto_inject_response` is enabled (default `true`),
SeoBundle will automatically inject SEO meta before `</head>` during the HTTP Response phase.

---

## 📘 Full Example

```yaml
# config/packages/gurtok_seo.yaml
gurtok_seo:
    allow_custom_meta: true
    auto_inject_response: true
    excluded_paths:
        - '/admin'
        - '/api'
```

Controller:

```php
use Gurtok\SeoBundle\Attribute\SeoMeta;

#[SeoMeta(
    title: 'Blog',
    description: 'Latest articles and news.',
    og: ['title' => 'Blog OG', 'type' => 'website'],
    twitter: ['card' => 'summary'],
)]
class BlogController
{
    public function __invoke()
    {
        // ...
    }
}
```

Twig:

```twig
<head>
    {{ seo() }}
</head>
```

Result in HTML:

```html
<title>Blog</title>
<meta name="description" content="Latest articles and news.">
<meta property="og:title" content="Blog OG">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary">
```

---

## 📄 License

SeoBundle is open-sourced software licensed under the [MIT license](LICENSE).

---
