<?php

namespace Gurtok\SeoBundle\Helper;

use Gurtok\SeoBundle\Model\Enum\FromWithPrefixInterface;

class EnumResolveHelper
{
    /**
     * @throws \ValueError
     */
    public static function resolve(
        \BackedEnum|int|string $value,
        string $enumClass,
        bool $allowedCustomValue = false,
    ): string|int|\BackedEnum {
        static::assertEnumClass($enumClass);

        if ($value instanceof $enumClass) {
            return $value;
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        /** @var class-string<\BackedEnum> $enumClass */
        if ($allowedCustomValue) {
            if (is_a($enumClass, FromWithPrefixInterface::class, true)) {
                $value = $enumClass::tryFromPrefixed($value) ?? $value;
            } else {
                $value = $enumClass::tryFrom($value) ?? $value;
            }
        } else {
            if (is_a($enumClass, FromWithPrefixInterface::class, true)) {
                $value = $enumClass::fromPrefixed($value);
            } else {
                $value = $enumClass::from($value);
            }
        }

        return $value;
    }

    /**
     * @phpstan-assert class-string<\BackedEnum> $enumClass
     *
     * @throws \ValueError
     */
    protected static function assertEnumClass(string $enumClass): void
    {
        if (!is_a($enumClass, \BackedEnum::class, true)) {
            throw new \ValueError('Enum class must be a BackedEnum');
        }
    }
}
