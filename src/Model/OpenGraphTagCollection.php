<?php

namespace Gurtok\SeoBundle\Model;

use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;

/**
 * @extends TagCollection<string|array<string, string>, string|array<string, string>>
 */
class OpenGraphTagCollection extends TagCollection
{
    protected function getTagClass(): string
    {
        return OpenGraphTag::class;
    }

    protected function getRealTagName(\BackedEnum|string $name): string
    {
        if (\is_string($name)) {
            return OpenGraphTag::prefix().str_replace(OpenGraphTag::prefix(), '', $name);
        }

        return (string) $name->value;
    }

    protected function isAllowedCustomTags(): bool
    {
        return false;
    }

    protected function isStringValue(string $tag): bool
    {
        return match ($tag) {
            OpenGraphTag::IMAGE->value,
            OpenGraphTag::TYPE->value,
            OpenGraphTag::LOCALE->value,
            OpenGraphTag::URL->value,
            OpenGraphTag::SITE_NAME->value => true,
            default => false,
        };
    }

    protected function isLocalizedValue(string $tag): bool
    {
        return match ($tag) {
            OpenGraphTag::TITLE->value,
            OpenGraphTag::DESCRIPTION->value => true,
            default => false,
        };
    }
}
