<?php

namespace Gurtok\SeoBundle\Model\Enum;

use Gurtok\SeoBundle\Model\Enum\Traits\FromWithPrefixTrait;

enum OpenGraphTag: string implements FromWithPrefixInterface
{
    use FromWithPrefixTrait;

    case TITLE = 'og:title';
    case DESCRIPTION = 'og:description';
    case IMAGE = 'og:image';
    case URL = 'og:url';
    case TYPE = 'og:type';
    case LOCALE = 'og:locale';
    case SITE_NAME = 'og:site_name';

    protected static function prefix(): string
    {
        return 'og:';
    }
}
