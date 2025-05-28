<?php

namespace Gurtok\SeoBundle\Model;

use Gurtok\SeoBundle\Model\Enum\MetaTag;

/**
 * @extends TagCollection<string|array<string, string>, string|array<string, string>>
 */
class MetaTagCollection extends TagCollection
{
    /**
     * @param array<string, string|array<string,string>> $tags
     */
    public function __construct(
        array $tags = [],
        protected bool $allowedCustomTags = false,
    ) {
        parent::__construct($tags);
    }

    protected function getTagClass(): string
    {
        return MetaTag::class;
    }

    protected function isAllowedCustomTags(): bool
    {
        return $this->allowedCustomTags;
    }

    protected function isStringValue(string $tag): bool
    {
        return match ($tag) {
            MetaTag::TITLE->value,
            MetaTag::DESCRIPTION->value,
            MetaTag::AUTHOR->value,
            MetaTag::KEYWORDS->value => false,
            MetaTag::ROBOTS->value,
            MetaTag::VIEWPORT->value,
            MetaTag::CHARSET->value,
            MetaTag::THEME_COLOR->value,
            MetaTag::GOOGLE->value,
            MetaTag::GOOGLEBOT->value,
            MetaTag::RATING->value => true,
            default => $this->isAllowedCustomTags(),
        };
    }

    protected function isLocalizedValue(string $tag): bool
    {
        return match ($tag) {
            MetaTag::TITLE->value,
            MetaTag::DESCRIPTION->value,
            MetaTag::AUTHOR->value,
            MetaTag::KEYWORDS->value => true,
            default => false,
        };
    }
}
