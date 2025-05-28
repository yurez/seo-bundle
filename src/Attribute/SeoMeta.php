<?php

namespace Gurtok\SeoBundle\Attribute;

use Gurtok\SeoBundle\Model\Enum\TwitterCardType;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class SeoMeta
{
    /**
     * @param array<string, string>|string|null $title
     * @param array<string, string>|string|null $titlePrefix
     * @param array<string, string>|string|null $description
     * @param array<string, string|array<string, string>> $meta
     * @param array<string, string|array<string, string>> $og
     * @param array<string, string|array<string, string>|TwitterCardType> $twitter
     * @param array<string, string> $verifications
     * @param array<string, string> $hreflangs
     */
    public function __construct(
        public readonly array|string|null $title = null,
        public readonly ?string $titleSeparator = null,
        public readonly array|string|null $titlePrefix = null,
        public readonly array|string|null $description = null,
        public readonly ?string $canonical = null,
        public readonly bool $autoGenerateCanonical = true,
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
         *     *    [string]: string|array<string, string>
         * }
         */
        public readonly array $og = [],
        /**
         * @var array{
         *      card?: string|TwitterCardType,
         *      title?: string|array,
         *      description?: string|array,
         *      image?: string,
         *      *   [string]: string|array<string, string>|TwitterCardType
         * }
         */
        public readonly array $twitter = [],
        public readonly array $verifications = [],
        public readonly array $hreflangs = [],
        public readonly bool $noIndex = false,
        public readonly bool $isAdultContent = false,
        public readonly bool $disableDefaults = false,
    ) {
    }
}
