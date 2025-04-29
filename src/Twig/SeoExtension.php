<?php

namespace Gurtok\SeoBundle\Twig;

use Gurtok\SeoBundle\Service\SeoManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function __construct(private readonly SeoManager $seoManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seo', [$this, 'renderAll'], ['is_safe' => ['html']]),
            new TwigFunction('seo_meta', [$this, 'renderMeta'], ['is_safe' => ['html']]),
            new TwigFunction('seo_og', [$this, 'renderOpenGraph'], ['is_safe' => ['html']]),
            new TwigFunction('seo_open_graph', [$this, 'renderOpenGraph'], ['is_safe' => ['html']]),
            new TwigFunction('seo_twitter', [$this, 'renderTwitter'], ['is_safe' => ['html']]),
            new TwigFunction('seo_hreflangs', [$this, 'renderHreflangs'], ['is_safe' => ['html']]),
            new TwigFunction('seo_verification', [$this, 'renderVerification'], ['is_safe' => ['html']]),
        ];
    }

    public function renderMeta(): string
    {
        $meta = $this->seoManager->get();
        $html = '';

        if ($meta->title) {
            $html .= \sprintf("<title>%s</title>\n", htmlspecialchars($meta->title));
        }
        if ($meta->description) {
            $html .= \sprintf('<meta name="description" content="%s">'."\n", htmlspecialchars($meta->description));
        }
        if ($meta->canonical) {
            $html .= \sprintf('<link rel="canonical" href="%s">'."\n", htmlspecialchars($meta->canonical));
        }

        foreach ($meta->meta as $name => $content) {
            $html .= \sprintf('<meta name="%s" content="%s">'."\n", htmlspecialchars($name), htmlspecialchars($content));
        }

        return $html;
    }

    public function renderOpenGraph(): string
    {
        $meta = $this->seoManager->get();
        $html = '';

        foreach ($meta->og as $property => $content) {
            $html .= \sprintf('<meta property="%s" content="%s">'."\n", htmlspecialchars($property), htmlspecialchars($content));
        }

        return $html;
    }

    public function renderTwitter(): string
    {
        $meta = $this->seoManager->get();
        $html = '';

        foreach ($meta->twitter as $name => $content) {
            $html .= \sprintf('<meta name="%s" content="%s">'."\n", htmlspecialchars($name), htmlspecialchars($content));
        }

        return $html;
    }

    public function renderHreflangs(): string
    {
        $meta = $this->seoManager->get();
        $html = '';

        foreach ($meta->hreflangs as $lang => $url) {
            $html .= \sprintf('<link rel="alternate" hreflang="%s" href="%s">'."\n", htmlspecialchars($lang), htmlspecialchars($url));
        }

        return $html;
    }

    public function renderVerification(): string
    {
        $meta = $this->seoManager->get();
        $html = '';

        foreach ($meta->verifications as $name => $content) {
            $html .= \sprintf('<meta name="%s" content="%s">'."\n", htmlspecialchars($name), htmlspecialchars($content));
        }

        return $html;
    }

    public function renderAll(): string
    {
        $this->seoManager->markAsRendered();

        return $this->renderMeta()
            .$this->renderOpenGraph()
            .$this->renderTwitter()
            .$this->renderHreflangs()
            .$this->renderVerification();
    }
}
