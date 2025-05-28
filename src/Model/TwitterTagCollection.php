<?php

namespace Gurtok\SeoBundle\Model;

use Gurtok\SeoBundle\Exception\InvalidTagValueException;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;

/**
 * @extends TagCollection<string|array<string, string>|TwitterCardType, string|array<string, string>>
 */
class TwitterTagCollection extends TagCollection
{
    protected function getTagClass(): string
    {
        return TwitterCardTag::class;
    }

    protected function getRealTagName(\BackedEnum|string $name): string
    {
        if (\is_string($name)) {
            return TwitterCardTag::prefix().str_replace(TwitterCardTag::prefix(), '', $name);
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
            TwitterCardTag::CARD->value => false, // TwitterCardType is not a string
            TwitterCardTag::IMAGE->value => true,
            default => false,
        };
    }

    protected function isLocalizedValue(string $tag): bool
    {
        return match ($tag) {
            TwitterCardTag::TITLE->value,
            TwitterCardTag::DESCRIPTION->value => true,
            default => false,
        };
    }

    protected function assertValue(string $tag, mixed $value): void
    {
        if ($tag === TwitterCardTag::CARD->value) {
            if (
                !($value instanceof TwitterCardType)
                && !(\is_string($value) && null !== TwitterCardType::tryFrom($value))
            ) {
                throw new InvalidTagValueException($tag, $value, 'Must be a valid TwitterCardType enum or string value from TwitterCardType enum');
            }

            return;
        }

        parent::assertValue($tag, $value);
    }

    protected function normalizeValue(string $tag, mixed $value): string|array
    {
        if ($tag === TwitterCardTag::CARD->value) {
            if ($value instanceof TwitterCardType) {
                return $value->value;
            }
            $this->assertString($value, $tag);

            return $value;
        }

        return parent::normalizeValue($tag, $value);
    }
}
