<?php

namespace Gurtok\SeoBundle\Exception;

class InvalidTagValueException extends \InvalidArgumentException
{
    public function __construct(string $tag, mixed $value, string $message = '')
    {
        if ($message) {
            $message = \sprintf(' (%s)', $message);
        }

        $message = \sprintf('The value "%s" for the tag "%s" is invalid%s.',
            \is_array($value) ? print_r($value, true) : (\is_scalar($value) ? $value : \gettype($value)),
            $tag,
            $message
        );

        parent::__construct($message);
    }
}
