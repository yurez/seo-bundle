<?php

namespace Gurtok\SeoBundle\Service;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;

class SeoTagHtmlBuilder
{
    /**
     * @param array<string, array{0: string, 1: string}> $adultContentMetaTags
     */
    public function __construct(
        protected SeoManager $seoManager,
        protected array $adultContentMetaTags = [
            'rating' => ['rating', 'adult'],
            'RTA' => ['rating', 'RTA-5042-1996-1400-1577-RTA'],
        ],
    ) {
    }

    public function buildTitle(bool $skipEmpty = true): string
    {
        $title = $this->seoManager->getFullTitle();

        if (empty($title) && $skipEmpty) {
            return '';
        }

        return \sprintf('<title>%s</title>', htmlentities($title ?? ''));
    }

    public function buildDescription(bool $skipEmpty = true): string
    {
        $description = $this->seoManager->getDescription();

        if (empty($description) && $skipEmpty) {
            return '';
        }

        return $this->buildMetaTagHTML(
            $this->seoManager->getMetaTag(MetaTag::DESCRIPTION),
            $description ?? ''
        );
    }

    public function buildMetaTag(string|MetaTag $tag, bool $skipEmpty = true): string
    {
        $value = $this->seoManager->getMeta($tag);

        if (empty($value) && $skipEmpty) {
            return '';
        }

        return $this->buildMetaTagHTML(
            $this->seoManager->getMetaTag($tag),
            $value ?? ''
        );
    }

    public function buildNoIndex(): string
    {
        if ($this->seoManager->isNoIndex()) {
            return '<meta name="robots" content="noindex, nofollow">';
        }

        return '';
    }

    public function buildAdultContent(): string
    {
        if ($this->seoManager->isAdultContent()) {
            $html = [];
            if (empty($this->adultContentMetaTags)) {
                $html[] = $this->buildMetaTagHTML('rating', 'adult');
            } else {
                foreach ($this->adultContentMetaTags as $name => $config) {
                    if (!\is_array($config) || 2 !== \count($config)) {
                        throw new \InvalidArgumentException(\sprintf('Invalid configuration for adult content meta tag "%s". Expected array with two elements.', $name));
                    }

                    $html[] = $this->buildMetaTagHTML($config[0], $config[1]);
                }
            }

            return implode("\n", $html);
        }

        return '';
    }

    public function buildAllMetaTags(bool $includeTitle = true, bool $skipEmpty = true): string
    {
        $html = [];

        if ($includeTitle) {
            $html[] = $this->buildTitle($skipEmpty);
        }

        $html[] = $this->buildDescription($skipEmpty);

        $html[] = $this->buildNoIndex();

        foreach ($this->seoManager->getMetaCollection()->keys() as $tag) {
            if ($tag === MetaTag::ROBOTS->value && $this->seoManager->isNoIndex()) {
                continue;
            }
            if ($tag === MetaTag::TITLE->value || $tag === MetaTag::DESCRIPTION->value) {
                continue;
            }
            $html[] = $this->buildMetaTag($tag, $skipEmpty);
        }

        return trim(implode("\n", array_filter($html)));
    }

    public function buildOpenGraphTag(string|OpenGraphTag $tag): string
    {
        $value = $this->seoManager->getOpenGraph($tag);

        if (empty($value)) {
            return '';
        }

        return $this->buildMetaTagHTML(
            $this->seoManager->getOpenGraphTag($tag),
            $value,
            'property'
        );
    }

    public function buildAllOpenGraphTags(): string
    {
        $html = [];

        foreach ($this->seoManager->getOpenGraphCollection()->keys() as $tag) {
            $html[] = $this->buildOpenGraphTag($tag);
        }

        return implode("\n", array_filter($html));
    }

    public function buildTwitterTag(string|TwitterCardTag $tag): string
    {
        $value = $this->seoManager->getTwitter($tag);

        if (empty($value)) {
            return '';
        }

        return $this->buildMetaTagHTML(
            $this->seoManager->getTwitterTag($tag),
            $value
        );
    }

    public function buildAllTwitterTags(): string
    {
        $html = [];

        foreach ($this->seoManager->getTwitterCollection()->keys() as $tag) {
            $html[] = $this->buildTwitterTag($tag);
        }

        return implode("\n", array_filter($html));
    }

    public function buildCanonical(): string
    {
        $canonical = $this->seoManager->getCanonical();

        if (empty($canonical)) {
            return '';
        }

        return \sprintf(
            '<link rel="canonical" href="%s">',
            htmlentities($canonical)
        );
    }

    public function buildVerificationTags(): string
    {
        $html = [];

        foreach ($this->seoManager->getVerifications() as $name => $value) {
            $html[] = $this->buildMetaTagHTML($name, $value);
        }

        return implode("\n", $html);
    }

    public function buildHreflangs(): string
    {
        $html = [];

        foreach ($this->seoManager->getHreflangs() as $lang => $url) {
            $html[] = \sprintf(
                '<link rel="alternate" hreflang="%s" href="%s">',
                htmlentities($lang),
                htmlentities($url)
            );
        }

        return implode("\n", $html);
    }

    public function buildAllTags(bool $includeTitle = true, bool $skipEmpty = true): string
    {
        return implode("\n", array_filter([
            $this->buildAllMetaTags($includeTitle, $skipEmpty),
            $this->buildAdultContent(),
            $this->buildAllOpenGraphTags(),
            $this->buildAllTwitterTags(),
            $this->buildCanonical(),
            $this->buildVerificationTags(),
            $this->buildHreflangs(),
        ]));
    }

    protected function buildMetaTagHTML(
        string $name,
        string $value,
        string $nameAttribute = 'name',
        string $valueAttribute = 'content',
    ): string {
        return \sprintf(
            '<meta %s="%s" %s="%s">',
            $nameAttribute,
            htmlentities($name),
            $valueAttribute,
            htmlentities($value)
        );
    }
}
