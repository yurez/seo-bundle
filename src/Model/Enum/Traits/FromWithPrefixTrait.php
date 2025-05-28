<?php

namespace Gurtok\SeoBundle\Model\Enum\Traits;

/**
 * @static array cases()
 */
trait FromWithPrefixTrait
{
    public function getCleanValue(): string|int
    {
        $value = $this->value;
        if (\is_int($value)) {
            return $value;
        }
        if (str_starts_with($value, static::prefix())) {
            return substr($value, \strlen(static::prefix()));
        }

        return $value;
    }

    public static function fromPrefixed(int|string $value): static
    {
        $value = static::normalizeValue($value);

        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        throw new \ValueError(\sprintf('Value "%s" is not a valid enum case for %s.', $value, static::class));
    }

    public static function tryFromPrefixed(int|string $value): ?static
    {
        $value = static::normalizeValue($value);

        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    private static function normalizeValue(int|string $value): string
    {
        if (!str_starts_with((string) $value, static::prefix())) {
            return static::prefix().$value;
        }

        return (string) $value;
    }

    abstract protected static function prefix(): string;
}
