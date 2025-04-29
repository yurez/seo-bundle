<?php

namespace Gurtok\SeoBundle\EventListener;

use Gurtok\SeoBundle\Service\SeoManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as Twig;

#[AsEventListener(event: KernelEvents::RESPONSE)]
class SeoResponseListener
{
    public function __construct(
        private readonly SeoManager $seoManager,
        private readonly Twig $twig,
        private readonly bool $autoInjectEnabled = true,
        private readonly array $excludedPaths = [],
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$this->autoInjectEnabled) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$this->isHtmlResponse($response)) {
            return;
        }

        if ($this->isExcludedPath($request->getPathInfo())) {
            return;
        }

        if ($this->seoManager->isRendered()) {
            return;
        }

        $content = $response->getContent();
        if (!$content || !str_contains($content, '</head>')) {
            return;
        }

        $seoHtml = $this->buildSeoHtml();
        if (!$seoHtml) {
            return;
        }

        $updatedContent = str_replace('</head>', $seoHtml.'</head>', $content);
        $this->seoManager->markAsRendered();

        $response->setContent($updatedContent);
    }

    private function isHtmlResponse(Response $response): bool
    {
        return str_contains($response->headers->get('Content-Type', ''), 'text/html');
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

    private function buildSeoHtml(): string
    {
        return $this->twig->render('@SeoBundle/seo/full.html.twig', [
            'seo' => $this->seoManager->get(),
        ]);
    }
}
