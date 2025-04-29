<?php

namespace Gurtok\SeoBundle\Model\Enum;

enum MetaTag: string
{
    case TITLE = 'title';
    case DESCRIPTION = 'description';
    case KEYWORDS = 'keywords';
    case ROBOTS = 'robots';
}
