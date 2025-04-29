<?php

namespace Gurtok\SeoBundle\Model\Enum;

enum TwitterCardType: string
{
    case SUMMARY = 'summary';
    case SUMMARY_LARGE_IMAGE = 'summary_large_image';
    case APP = 'app';
    case PLAYER = 'player';
    case GALLERY = 'gallery';
    case PHOTO = 'photo';
}
