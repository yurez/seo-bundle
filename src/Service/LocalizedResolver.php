<?php

namespace Gurtok\SeoBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocalizedResolver implements LocalizedResolverInterface
{
    protected const DEFAULT_VALUE = 'default';

    protected ?string $locale = null;
    protected ?string $translationDomain = null;

    public function __construct(
        protected RequestStack $requestStack,
        protected ?TranslatorInterface $translator,
        protected string $defaultLocale = 'en',
    ) {
    }

    /**
     * @param array<string, string>|string|null $value
     */
    public function resolveValue(array|string|null $value): ?string
    {
        if (\is_string($value)) {
            return $this->trans($value);
        }

        if (\is_array($value)) {
            $result = $value[$this->getLocale()]
                ?? $value[$this->defaultLocale]
                ?? $value[static::DEFAULT_VALUE]
                ?? reset($value) ?: null;

            if (isset($value[static::DEFAULT_VALUE]) || 1 === \count($value)) {
                return $this->trans($result);
            }

            return $result;
        }

        return null;
    }

    public function setTranslationDomain(?string $translationDomain): static
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    protected function getLocale(): string
    {
        return $this->locale ?: $this->requestStack->getMainRequest()?->getLocale() ?: $this->defaultLocale;
    }

    protected function trans(?string $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return $this->translator?->trans(
            $value,
            domain: $this->translationDomain,
            locale: $this->getLocale()
        ) ?: $value;
    }
}
