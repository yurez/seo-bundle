<?php

namespace Gurtok\SeoBundle\Helper;

class EnumStringValueResolveHelper
{
    public static function resolve(\BackedEnum|string $value): string
    {
        return (string) ($value instanceof \BackedEnum ? $value->value : $value);
    }
}
