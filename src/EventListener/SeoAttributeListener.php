<?php

namespace Gurtok\SeoBundle\EventListener;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\Model\Enum\FromWithPrefixInterface;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Service\SeoManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: KernelEvents::CONTROLLER)]
class SeoAttributeListener
{
    public function __construct(
        protected readonly SeoManager $seoManager,
        protected readonly RequestStack $requestStack,
        protected readonly ?TranslatorInterface $translator,
        protected readonly string $defaultLocale = 'en',
        protected readonly bool $supportCustomMetaTags = false,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (!\is_array($controller)) {
            return;
        }

        [$controllerObject, $actionMethod] = $controller;

        $reflectionClass = new \ReflectionClass($controllerObject);
        $reflectionMethod = $reflectionClass->getMethod($actionMethod);

        $attribute = $reflectionMethod->getAttributes(
            SeoMeta::class,
            \ReflectionAttribute::IS_INSTANCEOF
        )[0] ?? null;
        if (!$attribute) {
            $attribute = $reflectionClass->getAttributes(
                SeoMeta::class,
                \ReflectionAttribute::IS_INSTANCEOF
            )[0] ?? null;
        }

        if (!$attribute) {
            return;
        }
        $attribute = $attribute->newInstance();

        $locale = $this->requestStack->getMainRequest()?->getLocale() ?? $this->defaultLocale;

        $this->processSeoMeta($attribute, $locale, $this->defaultLocale);
    }

    protected function processSeoMeta(SeoMeta $seoMeta, $locale, $defaultLocale = 'en'): void
    {
        if (null !== $seoMeta->title) {
            $this->seoManager->setTitle(
                $this->resolveLocalizedValue($seoMeta->title, $locale, $defaultLocale)
            );
        }

        if (null !== $seoMeta->description) {
            $this->seoManager->setDescription(
                $this->resolveLocalizedValue($seoMeta->description, $locale, $defaultLocale)
            );
        }

        if (null !== $seoMeta->canonical) {
            $this->seoManager->setCanonical($seoMeta->canonical);
        }

        foreach ($seoMeta->meta as $name => $content) {
            $name = $this->resolveEnum($name, MetaTag::class, $this->supportCustomMetaTags);
            /** @var MetaTag $name */
            $this->seoManager->addMeta(
                $name,
                $this->resolveLocalizedValue($content, $locale, $defaultLocale) ?? $content
            );
        }

        foreach ($seoMeta->og as $property => $content) {
            /** @var OpenGraphTag $property */
            $property = $this->resolveEnum($property, OpenGraphTag::class);
            $this->seoManager->addOg(
                $property,
                $this->resolveLocalizedValue($content, $locale, $defaultLocale) ?? $content
            );
        }

        foreach ($seoMeta->twitter as $name => $content) {
            /** @var TwitterCardTag $name */
            $name = $this->resolveEnum($name, TwitterCardTag::class);
            $this->seoManager->addTwitter(
                $name,
                $this->resolveLocalizedValue($content, $locale, $defaultLocale) ?? $content
            );
        }

        foreach ($seoMeta->verifications as $name => $value) {
            $this->seoManager->addVerification($name, $value);
        }

        if ($seoMeta->hreflangs) {
            $this->seoManager->setHreflangs($seoMeta->hreflangs);
        }
    }

    protected function resolveLocalizedValue(
        array|string|null $value,
        string $currentLocale,
        string $defaultLocale = 'en',
    ): ?string {
        if (\is_string($value)) {
            return $this->translator?->trans($value) ?? $value;
        }

        if (\is_array($value)) {
            return $value[$currentLocale]
                ?? $value[$defaultLocale]
                ?? reset($value);
        }

        return null;
    }

    protected function resolveEnum(mixed $name, string $enumClass, bool $allowedCustomNames = false): \BackedEnum|string
    {
        if ($name instanceof \BackedEnum) {
            return $name;
        }
        if ($allowedCustomNames) {
            if (is_a($enumClass, FromWithPrefixInterface::class, true)) {
                $name = $enumClass::tryFromPrefixed($name) ?? $name;
            } else {
                $name = $enumClass::tryFrom($name) ?? $name;
            }
        } else {
            if (is_a($enumClass, FromWithPrefixInterface::class, true)) {
                $name = $enumClass::fromPrefixed($name) ?? $name;
            } else {
                $name = $enumClass::tryFrom($name) ?? $name;
            }
        }

        return $name;
    }
}
