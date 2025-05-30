<?php

namespace Gurtok\SeoBundle\EventListener;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\Helper\EnumResolveHelper;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Service\CanonicalUrlGeneratorInterface;
use Gurtok\SeoBundle\Service\SeoManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::CONTROLLER)]
final class SeoAttributeListener
{
    public function __construct(
        private readonly SeoManager $seoManager,
        private readonly CanonicalUrlGeneratorInterface $canonicalUrlGenerator,
        private readonly bool $supportCustomMetaTags = false,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (\is_object($controller)) {
            $controller = [$controller, '__invoke'];
        }
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

        /** @var SeoMeta $attribute */
        if ($attribute->disableDefaults) {
            $this->seoManager->reset();
        }
        $this->processSeoMeta($attribute, $event->getRequest());
    }

    private function processSeoMeta(SeoMeta $seoMeta, Request $request): void
    {
        if (null !== $seoMeta->title) {
            $this->seoManager->setTitle($seoMeta->title);
        }

        if (null !== $seoMeta->titleSeparator) {
            $this->seoManager->setTitleSeparator($seoMeta->titleSeparator);
        }

        if (null !== $seoMeta->titlePrefix) {
            $this->seoManager->setTitlePrefix($seoMeta->titlePrefix);
        }

        if (null !== $seoMeta->description) {
            $this->seoManager->setDescription($seoMeta->description);
        }

        if (null !== $seoMeta->canonical) {
            $this->seoManager->setCanonical($seoMeta->canonical);
        } elseif ($seoMeta->autoGenerateCanonical) {
            $this->seoManager->setCanonical(
                $this->canonicalUrlGenerator->generateFromRequest($request)
            );
        } else {
            $this->seoManager->setCanonical(null);
        }

        foreach ($seoMeta->meta as $name => $content) {
            /** @var MetaTag|string $name */
            $name = EnumResolveHelper::resolve($name, MetaTag::class, $this->supportCustomMetaTags);
            $this->seoManager->addMeta($name, $content);
        }

        foreach ($seoMeta->og as $property => $content) {
            /** @var OpenGraphTag $property */
            $property = EnumResolveHelper::resolve($property, OpenGraphTag::class);
            $this->seoManager->addOpenGraph($property, $content);
        }

        foreach ($seoMeta->twitter as $name => $content) {
            /** @var TwitterCardTag $name */
            $name = EnumResolveHelper::resolve($name, TwitterCardTag::class);
            $this->seoManager->addTwitter($name, $content);
        }

        foreach ($seoMeta->verifications as $name => $value) {
            $this->seoManager->addVerification($name, $value);
        }

        if ($seoMeta->hreflangs) {
            $this->seoManager->setHreflangs($seoMeta->hreflangs);
        }

        if ($seoMeta->isAdultContent) {
            $this->seoManager->markContentAsAdult();
        }

        if ($seoMeta->noIndex) {
            $this->seoManager->markAsNoIndex();
        }

        if (null !== $seoMeta->translationDomain) {
            $this->seoManager->setTranslationDomain($seoMeta->translationDomain);
        }
    }
}
