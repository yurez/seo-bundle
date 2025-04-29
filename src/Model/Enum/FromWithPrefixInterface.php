<?php

namespace Gurtok\SeoBundle\Model\Enum;

interface FromWithPrefixInterface
{
    public static function fromPrefixed(int|string $value): static;

    public static function tryFromPrefixed(int|string $value): ?static;
}
