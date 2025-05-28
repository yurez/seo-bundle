<?php

namespace Gurtok\SeoBundle\Model\Enum;

enum MetaTag: string
{
    case TITLE = 'title';
    case DESCRIPTION = 'description';
    case KEYWORDS = 'keywords';
    case ROBOTS = 'robots';
    case AUTHOR = 'author';
    case VIEWPORT = 'viewport';
    case CHARSET = 'charset';
    case THEME_COLOR = 'theme-color';
    case GOOGLE = 'google';
    case GOOGLEBOT = 'googlebot';
    case RATING = 'rating';
}
