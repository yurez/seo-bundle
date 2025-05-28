<?php

namespace Gurtok\SeoBundle\EventListener;

use Gurtok\SeoBundle\Service\CanonicalUrlGenerator;
use Gurtok\SeoBundle\Service\SeoDefaultsProvider;
use Gurtok\SeoBundle\Service\SeoManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST)]
final class SeoDefaultsListener
{
    /**
     * @param array<string> $excludedPaths
     */
    public function __construct(
        private readonly SeoManager $seoManager,
        private readonly SeoDefaultsProvider $seoDefaultsProvider,
        private readonly CanonicalUrlGenerator $canonicalUrlGenerator,
        private readonly array $excludedPaths = [],
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($this->isExcludedPath($request->getPathInfo())) {
            return;
        }

        if ($this->seoManager->isRendered()) {
            return;
        }

        $this->processDefaults($request);
    }

    private function processDefaults(Request $request): void
    {
        if (null !== $this->seoDefaultsProvider->getTitle()) {
            $this->seoManager->setTitle($this->seoDefaultsProvider->getTitle());
        }
        if (null !== $this->seoDefaultsProvider->getDescription()) {
            $this->seoManager->setDescription($this->seoDefaultsProvider->getDescription());
        }
        if ($this->seoDefaultsProvider->isAutoGenerateCanonical()) {
            $this->seoManager->setCanonical(
                $this->canonicalUrlGenerator->generateFromRequest($request)
            );
        }

        $this->seoManager
            ->setTitleSeparator($this->seoDefaultsProvider->getTitleSeparator())
            ->setMetaCollection($this->seoDefaultsProvider->getMetaCollection())
            ->setOpenGraphCollection($this->seoDefaultsProvider->getOpenGraphCollection())
            ->setTwitterCollection($this->seoDefaultsProvider->getTwitterCollection());

        foreach ($this->seoDefaultsProvider->getVerifications() as $name => $value) {
            $this->seoManager->addVerification($name, $value);
        }

        if ($this->seoDefaultsProvider->isAdultContent()) {
            $this->seoManager->markContentAsAdult();
        }

        if ($this->seoDefaultsProvider->isNoIndex()) {
            $this->seoManager->markAsNoIndex();
        }
    }

    private function isExcludedPath(string $path): bool
    {
        foreach ($this->excludedPaths as $excluded) {
            if (str_starts_with($path, $excluded)) {
                return true;
            }
        }

        return false;
    }
}
