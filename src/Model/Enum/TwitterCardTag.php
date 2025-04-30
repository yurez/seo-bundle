<?php

namespace Gurtok\SeoBundle\Model\Enum;

use Gurtok\SeoBundle\Model\Enum\Traits\FromWithPrefixTrait;

enum TwitterCardTag: string implements FromWithPrefixInterface
{
    use FromWithPrefixTrait;

    case CARD = 'twitter:card';
    case TITLE = 'twitter:title';
    case DESCRIPTION = 'twitter:description';
    case IMAGE = 'twitter:image';

    public function isCard(): bool
    {
        return self::CARD === $this;
    }

    protected static function prefix(): string
    {
        return 'twitter:';
    }
}
