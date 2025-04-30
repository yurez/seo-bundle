<?php

namespace Gurtok\SeoBundle\EventListener;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\Helper\EnumResolveHelper;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
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
        if (!\is_object($controllerObject) || !\is_string($actionMethod)) {
            return;
        }
        $reflectionClass = new \ReflectionClass($controllerObject);
        if (!$reflectionClass->hasMethod($actionMethod)) {
            return;
        }
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

    protected function processSeoMeta(SeoMeta $seoMeta, string $locale, string $defaultLocale = 'en'): void
    {
        if (null !== $seoMeta->title
            && null !== $value = $this->resolveLocalizedValue($seoMeta->title, $locale, $defaultLocale)
        ) {
            $this->seoManager->setTitle($value);
        }

        if (null !== $seoMeta->description
            && null !== $value = $this->resolveLocalizedValue($seoMeta->description, $locale, $defaultLocale)
        ) {
            $this->seoManager->setDescription($value);
        }

        if (null !== $seoMeta->canonical) {
            $this->seoManager->setCanonical($seoMeta->canonical);
        }

        foreach ($seoMeta->meta as $name => $content) {
            $name = EnumResolveHelper::resolve($name, MetaTag::class, $this->supportCustomMetaTags);
            /** @var MetaTag $name */
            $content = $this->resolveLocalizedValue($content, $locale, $defaultLocale);
            if (null === $content) {
                continue;
            }
            $this->seoManager->addMeta($name, $content);
        }

        foreach ($seoMeta->og as $property => $content) {
            /** @var OpenGraphTag $property */
            $property = EnumResolveHelper::resolve($property, OpenGraphTag::class);
            $content = $this->resolveLocalizedValue($content, $locale, $defaultLocale);
            if (null === $content) {
                continue;
            }
            $this->seoManager->addOg($property, $content);
        }

        foreach ($seoMeta->twitter as $name => $content) {
            /** @var TwitterCardTag $name */
            $name = EnumResolveHelper::resolve($name, TwitterCardTag::class);
            if (!$content instanceof TwitterCardType) {
                $content = $this->resolveLocalizedValue($content, $locale, $defaultLocale);
            }
            if (null === $content) {
                continue;
            }
            $this->seoManager->addTwitter($name, $content);
        }

        foreach ($seoMeta->verifications as $name => $value) {
            $this->seoManager->addVerification($name, $value);
        }

        if ($seoMeta->hreflangs) {
            $this->seoManager->setHreflangs($seoMeta->hreflangs);
        }
    }

    /**
     * @param array<string, string>|string|null $value
     */
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
                ?? reset($value) ?: null;
        }

        return null;
    }
}
