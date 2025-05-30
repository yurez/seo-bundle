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
     * @param bool $autoGenerateCanonical if true, the canonical URL will be automatically generated based on the current request
     * @param array<string, string|array<string, string>> $meta
     * @param array<string, string|array<string, string>> $og
     * @param array<string, string|array<string, string>|TwitterCardType> $twitter
     * @param array<string, string> $verifications
     * @param array<string, string> $hreflangs
     * @param bool $noIndex if true, indicates that the page should not be indexed by search engines
     * @param bool $isAdultContent if true, indicates that the content is intended for adults only
     * @param bool $disableDefaults If true, disables the default values for title, description, etc.
     *                              This is useful when you want to set all values explicitly.
     * @param string|null $translationDomain Translation domain to be used for translatable strings in this context.
     *                                       If null, the default translation domain will be used.
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
        public readonly ?string $translationDomain = null,
    ) {
    }
}
