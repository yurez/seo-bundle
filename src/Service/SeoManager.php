<?php

namespace Gurtok\SeoBundle\Service;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
use Gurtok\SeoBundle\Model\MetaTagCollection;
use Gurtok\SeoBundle\Model\OpenGraphTagCollection;
use Gurtok\SeoBundle\Model\SeoMetadata;
use Gurtok\SeoBundle\Model\TwitterTagCollection;

class SeoManager
{
    protected SeoMetadata $data;
    protected bool $rendered = false;
    protected bool $isAdultContent = false;
    protected bool $isNoIndex = false;

    public function __construct(
        protected LocalizedResolverInterface $localizedResolver,
        protected readonly bool $supportCustomMetaTags = false,
    ) {
        $this->data = new SeoMetadata($this->supportCustomMetaTags);
    }

    public function getRawData(): SeoMetadata
    {
        return $this->data;
    }

    public function reset(): static
    {
        if ($this->isRendered()) {
            throw new \RuntimeException('Cannot reset already rendered seo parameters');
        }
        $this->data = new SeoMetadata($this->supportCustomMetaTags);
        $this->isAdultContent = false;
        $this->isNoIndex = false;

        return $this;
    }

    public function isRendered(): bool
    {
        return $this->rendered;
    }

    public function markAsRendered(): static
    {
        $this->rendered = true;

        return $this;
    }

    /**
     * @param string|array<string, string> $value
     */
    public function setTitle(string|array $value): static
    {
        $this->data->title = $value;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->resolve($this->data->title);
    }

    public function setTitleSeparator(string $value): static
    {
        $this->data->titleSeparator = $value;

        return $this;
    }

    public function getTitleSeparator(): string
    {
        return $this->data->titleSeparator ?? ' - ';
    }

    /**
     * @param string|array<string, string> $value
     */
    public function setTitlePrefix(string|array $value): static
    {
        $this->data->titlePrefix = $value;

        return $this;
    }

    public function getTitlePrefix(): ?string
    {
        return $this->resolve($this->data->titlePrefix);
    }

    public function getFullTitle(): ?string
    {
        $title = $this->getTitle();

        if ($prefix = $this->getTitlePrefix()) {
            return $title ? $prefix.$this->getTitleSeparator().$title : $prefix;
        }

        return $title;
    }

    /**
     * @param string|array<string, string> $value
     */
    public function setDescription(string|array $value): static
    {
        $this->data->description = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->resolve($this->data->description);
    }

    public function setCanonical(?string $url): static
    {
        $this->data->canonical = $url;

        return $this;
    }

    public function getCanonical(): ?string
    {
        return $this->data->canonical;
    }

    /**
     * @param string|array<string, string> $value
     */
    public function addMeta(MetaTag|string $tag, array|string $value): static
    {
        if (!$this->supportCustomMetaTags && \is_string($tag)) {
            $tag = MetaTag::from($tag);
        }
        if ($tag instanceof MetaTag) {
            $tag = $tag->value;
        }
        $this->data->meta[$tag] = $value;

        return $this;
    }

    public function getMeta(MetaTag|string $tag): ?string
    {
        $value = $this->data->meta->get($tag);

        return $this->resolve($value);
    }

    public function getMetaTag(MetaTag|string $tag): string
    {
        return $this->data->meta->getTag($tag);
    }

    public function setMetaCollection(MetaTagCollection $collection): static
    {
        $this->data->meta = $collection;

        return $this;
    }

    public function getMetaCollection(): MetaTagCollection
    {
        return $this->data->meta;
    }

    /**
     * @param array<string, string>|string $value
     */
    public function addOpenGraph(OpenGraphTag $tag, array|string $value): static
    {
        $this->data->og[$tag->value] = $value;

        return $this;
    }

    public function getOpenGraph(OpenGraphTag|string $tag): ?string
    {
        $value = $this->data->og->get($tag);

        return $this->resolve($value);
    }

    public function getOpenGraphTag(OpenGraphTag|string $tag): string
    {
        return $this->data->og->getTag($tag);
    }

    public function setOpenGraphCollection(OpenGraphTagCollection $collection): static
    {
        $this->data->og = $collection;

        return $this;
    }

    public function getOpenGraphCollection(): OpenGraphTagCollection
    {
        return $this->data->og;
    }

    /**
     * @param TwitterCardType|array<string, string>|string $value
     */
    public function addTwitter(TwitterCardTag $tag, TwitterCardType|array|string $value): static
    {
        if (TwitterCardTag::CARD === $tag && \is_string($value)) {
            $value = TwitterCardType::from($value);
        }

        if ($value instanceof TwitterCardType && !$tag->isCard()) {
            throw new \InvalidArgumentException('Twitter card type should use just for card tag.');
        }

        $this->data->twitter[$tag->value] = $value;

        return $this;
    }

    public function getTwitter(TwitterCardTag|string $tag): ?string
    {
        $value = $this->data->twitter->get($tag);

        return $this->resolve($value);
    }

    public function getTwitterTag(TwitterCardTag|string $tag): string
    {
        return $this->data->twitter->getTag($tag);
    }

    public function setTwitterCollection(TwitterTagCollection $collection): static
    {
        $this->data->twitter = $collection;

        return $this;
    }

    public function getTwitterCollection(): TwitterTagCollection
    {
        return $this->data->twitter;
    }

    public function addVerification(string $name, string $value): static
    {
        $this->data->verifications[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getVerifications(): array
    {
        return $this->data->verifications;
    }

    /**
     * @param array<string, string> $hreflangs
     */
    public function setHreflangs(array $hreflangs): static
    {
        $this->data->hreflangs = $hreflangs;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getHreflangs(): array
    {
        return $this->data->hreflangs;
    }

    public function isAdultContent(): bool
    {
        return $this->isAdultContent;
    }

    public function markContentAsAdult(): static
    {
        $this->isAdultContent = true;

        return $this;
    }

    public function isNoIndex(): bool
    {
        return $this->isNoIndex;
    }

    public function markAsNoIndex(): static
    {
        $this->isNoIndex = true;

        return $this;
    }

    public function setTranslationDomain(?string $translationDomain): static
    {
        $this->localizedResolver->setTranslationDomain($translationDomain);

        return $this;
    }

    public function setLocale(string $locale): static
    {
        $this->localizedResolver->setLocale($locale);

        return $this;
    }

    /**
     * Resolves a localized value, potentially translating it if necessary.
     * @param array<string, string>|string|null $value
     */
    protected function resolve(array|string|null $value): ?string
    {
        return $this->localizedResolver->resolveValue($value);
    }
}
