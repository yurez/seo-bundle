<?php

namespace Gurtok\SeoBundle\Attribute;

use Gurtok\SeoBundle\Model\Enum\TwitterCardType;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
readonly class SeoMeta
{
    public function __construct(
        public array|string|null $title = null,
        public array|string|null $description = null,
        public array|string|null $canonical = null,
        /**
         * @var array{
         *     title?: string|array,
         *     description?: string|array,
         *     keywords?: string|array,
         *     robots?: string,
         *     *     [string]: string, // if you want to add custom meta tags
         * }
         */
        public array $meta = [],
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
        public array $og = [],
        /**
         * @var array{
         *     card?: string|TwitterCardType,
         *     title?: string|array,
         *     description?: string|array,
         *     image?: string,
         * }
         */
        public array $twitter = [],
        public array $verifications = [],
        public array $hreflangs = [],
        public array $structuredData = [],
    ) {
    }
}
