# UPGRADE FROM 1.x TO 2.0

Version `2.0.0` introduces a redesigned SEO processing pipeline. While the core ideas remain the same, the internals were refactored for extensibility and flexibility. Below are all breaking and notable changes.

---

## 💥 BREAKING CHANGES

### 1. SeoMeta attribute updated

New parameters were added:

- `titlePrefix`: prepends text to the title
- `titleSeparator`: allows customizing title glue
- `noIndex`, `isAdultContent`, `disableDefaults`: flags controlling indexation, content type and default value fallback
- `autoGenerateCanonical`: controls automatic canonical generation

➡️ **Backward-compatible:** If you used existing parameters, no changes required.

---

### 2. Twig Extension now uses `SeoTagHtmlBuilder`

The Twig extension now delegates rendering to a new internal helper class. This enables custom rendering strategies and cleaner HTML generation.

New Twig functions remain backward-compatible:

| Function       | Affected | Notes                                                  |
|----------------|----------|--------------------------------------------------------|
| `seo()`        | ✅       | Now supports options `include_title` and `skip_empty`  |
| `seo_meta()`   | ✅       | Same as above                                          |
| `seo_title()`  | ✅       | Added in 2.0                                           |
| `seo_twitter()`| ✅       | -                                                      |

---

### 3. Configuration changes

The `gurtok_seo` configuration now has an expanded `defaults` section.

**Before:**
```yaml
gurtok_seo:
  allow_custom_meta: false
  auto_inject_response: true
```

**Now:**
```yaml
gurtok_seo:
  allow_custom_meta: false
  auto_inject_response: true
  excluded_paths: []
  canonical_excluded_query_keys:
    - utm_source
    - utm_medium
    - utm_campaign
    - utm_term
    - utm_content
    - ref
    - fbclid
    - page
  defaults:
    title: 'My default title'
    title_separator: ' - '
    description: 'My default desc'
    auto_canonical: true
    meta:
      keywords: 'seo'
    og:
      type: website
    twitter:
      card: summary
    verifications:
      google: 'code'
    no_index: false
    is_adult_content: false
```
### 4. Listener refactor

The following listeners were introduced or changed:

| Listener                | Lifecycle                | Description                                     |
|------------------------|--------------------------|--------------------------------------------------|
| `SeoDefaultsListener`  | `KernelEvents::REQUEST`   | 🆕 Applies default SEO values                   |
| `SeoAttributeListener` | `KernelEvents::CONTROLLER`| ✳️ Updated to support new flags in `SeoMeta`    |
| `SeoResponseListener`  | `KernelEvents::RESPONSE`  | 🆕 Injects SEO tags into HTML output if enabled |

➡️ `auto_inject_response` affects only `SeoResponseListener`, **not others**.
➡️ `defaults` affects only `SeoDefaultsListener`, **not others**, if empty it will be disabled.

## 🧪 New Helpers

Several new internal helper classes were introduced to improve modularity and extensibility:

### `SeoTagHtmlBuilder`

A dedicated helper responsible for generating HTML for SEO tags. It’s used internally by the Twig extension.

While not intended for direct use in most projects, it can be reused for custom rendering scenarios.

### `SeoMetaRenderOptions`

A value object that allows passing structured rendering options to Twig functions such as `seo()` and `seo_meta()`.

Supported options:

- `include_title` (bool): whether to include the `<title>` tag
- `skip_empty` (bool): whether to skip tags with empty values

These options allow more fine-grained control over rendered output.

---

## ✅ Backward Compatibility

Most changes in 2.0 are backward-compatible **by design**:

- All new configuration options and parameters use **default values**, so your existing configuration continues to work.
- Twig functions (`seo`, `seo_meta`, etc.) remain **unchanged** in name and basic usage.
- The `SeoMeta` attribute accepts all existing parameters and now includes new optional ones.

### ⚠️ Potential BC Breaks

You may need to take action **if**:

- You inject the `SeoMeta` attribute via positional arguments (named arguments are recommended now).
- You inject services like `SeoManager` via positional arguments (named arguments are recommended now).
- You decorate or replace internal services (`TwigExtension`, `SeoManager`, etc.).
- You relied on the exact internal structure of Twig-generated HTML — minor changes may be visible due to `SeoTagHtmlBuilder`.

---

## 🚀 Upgrade Strategy Summary

| Feature / Component        | Change Type      | Action Needed?                        |
|----------------------------|------------------|---------------------------------------|
| `SeoMeta` attribute        | Extended         | ✅ Optional — update to use new flags |
| `Twig` extension           | Internal rewrite | ❌ No — uses same function names      |
| `SeoManager` API           | Rewrited         | ❌ No — compatible                    |
| Configuration (`defaults`) | Extended         | ✅ Optional — review new structure    |
| Listeners                  | Refactored       | ✅ Optional — can now be disabled     |


## 📦 Install / Upgrade to 2.0

To upgrade to version 2.0, update your `composer.json`:

```bash
composer require gurtok/seo-bundle:^2.0
```

If you're already using the bundle and want to confirm the upgrade:

```bash
composer update gurtok/seo-bundle
```

To remain on the 1.x version for now:

```bash
composer require gurtok/seo-bundle:^1.0
```

Make sure to clear the Symfony cache after upgrade:

```bash
php bin/console cache:clear
```

If you're using environment-specific configurations (e.g., disabling listeners in tests), verify that your `config/packages/` and `config/packages/test/` folders reflect the correct overrides.

## 💬 Questions or Issues?

If you encounter any issues during the upgrade, please open a ticket on the GitHub repository. We welcome bug reports, feature requests, and contributions.

Thank you for supporting GurtokSeoBundle! 🌟

