<?php

namespace Gurtok\SeoBundle\Exception;

class UnsupportedTagException extends \InvalidArgumentException
{
    public function __construct(string $tag)
    {
        parent::__construct(\sprintf('The tag "%s" is not supported.', $tag));
    }
}
