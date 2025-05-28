<?php

namespace Gurtok\SeoBundle\Twig;

final class SeoMetaRenderOptions
{
    public function __construct(
        readonly public bool $includeTitle = true,
        readonly public bool $skipEmpty = true,
    ) {
    }

    /**
     * @param array{
     *     include_title?: bool,
     *     skip_empty?: bool,
     *  } $options
     */
    public static function fromArray(array $options): self
    {
        return new self(
            includeTitle: $options['include_title'] ?? true,
            skipEmpty: $options['skip_empty'] ?? true,
        );
    }
}
