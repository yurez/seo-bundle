<?php

namespace Gurtok\SeoBundle\Service;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
use Gurtok\SeoBundle\Model\SeoMetadata;

class SeoManager
{
    protected SeoMetadata $data;
    protected bool $rendered = false;

    public function __construct(protected readonly bool $supportCustomMetaTags = false)
    {
        $this->data = new SeoMetadata();
    }

    public function get(): SeoMetadata
    {
        return $this->data;
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

    public function setTitle(string $value): static
    {
        $this->data->title = $value;

        return $this;
    }

    public function setDescription(string $value): static
    {
        $this->data->description = $value;

        return $this;
    }

    public function setCanonical(string $url): static
    {
        $this->data->canonical = $url;

        return $this;
    }

    public function addMeta(MetaTag|string $tag, string $value): static
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

    public function addOg(OpenGraphTag $tag, string $value): static
    {
        $this->data->og[$tag->value] = $value;

        return $this;
    }

    public function addTwitter(TwitterCardTag $tag, TwitterCardType|string $value): static
    {
        if (TwitterCardTag::CARD === $tag && !$value instanceof TwitterCardType) {
            $value = TwitterCardType::from($value);
        }

        if ($value instanceof TwitterCardType && !$tag->isCard()) {
            throw new \InvalidArgumentException('Twitter card type should use just for card tag.');
        }

        $this->data->twitter[$tag->value] = $value instanceof TwitterCardType ? $value->value : $value;

        return $this;
    }

    public function addVerification(string $name, string $value): static
    {
        $this->data->verifications[$name] = $value;

        return $this;
    }

    /**
     * @param array<string, string> $hreflangs
     */
    public function setHreflangs(array $hreflangs): static
    {
        $this->data->hreflangs = $hreflangs;

        return $this;
    }
}
