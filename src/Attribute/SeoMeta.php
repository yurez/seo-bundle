<?php

namespace Gurtok\SeoBundle\Attribute;

use Gurtok\SeoBundle\Model\Enum\TwitterCardType;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class SeoMeta
{
    public function __construct(
        public readonly array|string|null $title = null,
        public readonly array|string|null $description = null,
        public readonly array|string|null $canonical = null,
        /**
         * @var array{
         *     title?: string|array,
         *     description?: string|array,
         *     keywords?: string|array,
         *     robots?: string,
         *     *     [string]: string, // if you want to add custom meta tags
         * }
         */
        public readonly array $meta = [],
        /**
         * @var array{
         *     title?: string|array,
         *     description?: string|array,
         *     image?: string,
         *     url?: string,
         *     type?: string,
         *     locale?: string,
         *     site_name?: string,
         * }
         */
        public readonly array $og = [],
        /**
         * @var array{
         *     card?: string|TwitterCardType,
         *     title?: string|array,
         *     description?: string|array,
         *     image?: string,
         * }
         */
        public readonly array $twitter = [],
        public readonly array $verifications = [],
        public readonly array $hreflangs = [],
        public readonly array $structuredData = [],
    ) {
    }
}
