<?php

namespace Gurtok\SeoBundle\Twig;

use Gurtok\SeoBundle\Service\SeoTagHtmlBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function __construct(
        private readonly SeoTagHtmlBuilder $seoTagHtmlBuilder,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seo', [$this, 'renderAll'], ['is_safe' => ['html']]),
            new TwigFunction('seo_title', [$this, 'renderTitle'], ['is_safe' => ['html']]),
            new TwigFunction('seo_meta', [$this, 'renderMeta'], ['is_safe' => ['html']]),
            new TwigFunction('seo_og', [$this, 'renderOpenGraph'], ['is_safe' => ['html']]),
            new TwigFunction('seo_open_graph', [$this, 'renderOpenGraph'], ['is_safe' => ['html']]),
            new TwigFunction('seo_twitter', [$this, 'renderTwitter'], ['is_safe' => ['html']]),
            new TwigFunction('seo_hreflangs', [$this, 'renderHreflangs'], ['is_safe' => ['html']]),
            new TwigFunction('seo_verification', [$this, 'renderVerification'], ['is_safe' => ['html']]),
        ];
    }

    public function renderTitle(): string
    {
        return $this->seoTagHtmlBuilder->buildTitle();
    }

    /**
     * @param SeoMetaRenderOptions|array{
     *     include_title?: bool,
     *     skip_empty?: bool,
     *  } $options
     */
    public function renderMeta(SeoMetaRenderOptions|array $options = []): string
    {
        if (\is_array($options)) {
            $options = SeoMetaRenderOptions::fromArray($options);
        }

        return $this->seoTagHtmlBuilder->buildAllMetaTags($options->includeTitle, $options->skipEmpty);
    }

    public function renderOpenGraph(): string
    {
        return $this->seoTagHtmlBuilder->buildAllOpenGraphTags();
    }

    public function renderTwitter(): string
    {
        return $this->seoTagHtmlBuilder->buildAllTwitterTags();
    }

    public function renderHreflangs(): string
    {
        return $this->seoTagHtmlBuilder->buildHreflangs();
    }

    public function renderVerification(): string
    {
        return $this->seoTagHtmlBuilder->buildVerificationTags();
    }

    /**
     * @param SeoMetaRenderOptions|array{
     *     include_title?: bool,
     *     skip_empty?: bool,
     *  } $options
     */
    public function renderAll(SeoMetaRenderOptions|array $options = []): string
    {
        if (\is_array($options)) {
            $options = SeoMetaRenderOptions::fromArray($options);
        }

        return $this->seoTagHtmlBuilder->buildAllTags($options->includeTitle, $options->skipEmpty);
    }
}
