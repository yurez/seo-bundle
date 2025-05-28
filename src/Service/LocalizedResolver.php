<?php

namespace Gurtok\SeoBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocalizedResolver
{
    protected const DEFAULT_VALUE = 'default';

    protected ?string $locale = null;

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
            return $this->translator?->trans($value) ?: $value;
        }

        if (\is_array($value)) {
            return $value[$this->getLocale()]
                ?? $value[$this->defaultLocale]
                ?? $value[static::DEFAULT_VALUE]
                ?? reset($value) ?: null;
        }

        return null;
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
}
