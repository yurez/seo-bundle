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
    auto_inject_response: true        # Auto-insert SEO meta into <head> if not manually called
    excluded_paths:                   # Exclude specific paths from auto SEO injection
        - '/admin'
        - '/api'
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
    ]
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

- `<title>` tag
- `<meta>` description
- `<meta>` robots
- OpenGraph tags
- Twitter card tags
- JSON-LD structured data (if configured)
- Canonical link
- Hreflang alternate links
- Verification meta tags

---

## 🔥 Auto Injection (Optional)

If you forget to call `{{ seo() }}` manually in Twig,  
and `auto_inject_response` is enabled (default `true`),  
SeoBundle will automatically inject SEO meta before `</head>` during the HTTP Response phase.

---

## 📚 Full Example

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

