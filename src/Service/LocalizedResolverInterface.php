<?php

namespace Gurtok\SeoBundle\Service;

/**
 * Interface for resolving localized values with optional translation support.
 */
interface LocalizedResolverInterface
{
    /**
     * @param array<string, string>|string|null $value
     */
    public function resolveValue(array|string|null $value): ?string;

    /**
     * Sets the translation domain for resolving slugs via the translator.
     */
    public function setTranslationDomain(?string $translationDomain): static;

    public function setLocale(string $locale): static;
}
