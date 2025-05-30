# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] – 2025-05-30

### Fixed
- `LocalizedResolver::resolveValue()` now correctly translates values when:
  - a single-item array is passed (e.g. `['en' => 'Hello']`)
  - the array contains a `default` key (e.g. `['default' => 'Fallback value']`)

## [2.0.0] - 2025-05-29

### ⚠️ Breaking Changes

- Refactored the entire SEO processing pipeline to improve flexibility and extensibility.
- `SeoMeta` attribute was extended with new parameters:
    - `titlePrefix`, `titleSeparator`
    - `autoGenerateCanonical`, `noIndex`, `isAdultContent`, `disableDefaults`
- Internal Twig logic was restructured to use a new `SeoTagHtmlBuilder` helper class.
- SEO listeners have been refactored and split into:
    - `SeoDefaultsListener` (`KernelEvents::REQUEST`) – applies default SEO settings
    - `SeoAttributeListener` (`KernelEvents::CONTROLLER`) – processes `SeoMeta` attributes
    - `SeoResponseListener` (`KernelEvents::RESPONSE`) – injects tags into HTML
- The configuration tree now includes:
    - `defaults` section (title, description, meta, og, twitter, etc.)
    - `excluded_paths`
    - `canonical_excluded_query_keys`

### ✨ Added

- New Twig functions:
    - `seo_title()`
    - Optional rendering flags: `include_title`, `skip_empty` for `seo()` and `seo_meta()`
- New services:
    - `SeoTagHtmlBuilder`: handles SEO HTML generation for Twig
    - `SeoMetaRenderOptions`: allows passing options to Twig SEO renderers
- New config flags: `auto_inject_response`, `allow_custom_meta`

### 🧼 Changed

- Canonical URL generation moved to `CanonicalUrlGenerator` service.
- Configuration loading logic moved into `SeoDefaultsProvider`.

### 🧪 Tests

- Functional test coverage added for:
    - SEO injection
    - SEO defaults
    - Listener disabling logic

### ✅ Compatibility

- Default values ensure backward compatibility if older `SeoMeta` parameters were used.
- Twig function names preserved.
- Public API of `SeoManager` mostly intact.

---

## [1.0.0] - 2025-05-01

- Initial release.
- `SeoMeta` attribute for defining SEO metadata.
- Twig functions for rendering SEO tags.
- `SeoManager` service for managing SEO state.
- Listeners for injecting SEO tags into responses.
