<?php

namespace Gurtok\SeoBundle\Service;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\MetaTagCollection;
use Gurtok\SeoBundle\Model\OpenGraphTagCollection;
use Gurtok\SeoBundle\Model\TwitterTagCollection;

class SeoDefaultsProvider
{
    protected readonly MetaTagCollection $metaTags;
    protected readonly OpenGraphTagCollection $ogTags;
    protected readonly TwitterTagCollection $twitterTags;

    /**
     * @param array<string, array<string, string>|string|bool> $defaults
     */
    public function __construct(
        /**
         * @param array{
         *     title?: string|array,
         *     title_separator?: string,
         *     description?: string|array,
         *     auto_canonical?: bool,
         *     meta?: array<string, array>,
         *     og?: array<string, array>,
         *     twitter?: array<string, array>,
         *     verifications?: array<string, string>,
         *     no_index?: bool,
         *     is_adult_content?: bool
         * } $defaults
         */
        protected readonly array $defaults,
        bool $supportCustomMetaTags = false,
    ) {
        $this->assertDefaultsArray($this->defaults);
        $this->metaTags = new MetaTagCollection($this->defaults['meta'] ?? [], $supportCustomMetaTags);
        $this->ogTags = new OpenGraphTagCollection($this->defaults['og'] ?? []);
        $this->twitterTags = new TwitterTagCollection($this->defaults['twitter'] ?? []);
    }

    /**
     * @return string|array<string, string>|null
     */
    public function getTitle(): string|array|null
    {
        return $this->defaults['title'] ?? null;
    }

    public function getTitleSeparator(): string
    {
        return $this->defaults['title_separator'] ?? ' - ';
    }

    /**
     * @return string|array<string, string>|null
     */
    public function getDescription(): string|array|null
    {
        return $this->defaults['description'] ?? null;
    }

    /**
     * @return string|array<string, string>|null
     */
    public function getMeta(MetaTag|string $tag): string|array|null
    {
        return $this->metaTags->get($tag);
    }

    public function getMetaCollection(): MetaTagCollection
    {
        return $this->metaTags;
    }

    /**
     * @return string|array<string, string>|null
     */
    public function getOpenGraph(OpenGraphTag|string $tag): string|array|null
    {
        return $this->ogTags->get($tag);
    }

    public function getOpenGraphCollection(): OpenGraphTagCollection
    {
        return $this->ogTags;
    }

    /**
     * @return string|array<string, string>|null
     */
    public function getTwitter(TwitterCardTag|string $tag): string|array|null
    {
        return $this->twitterTags->get($tag);
    }

    public function getTwitterCollection(): TwitterTagCollection
    {
        return $this->twitterTags;
    }

    /**
     * @return array<string, string>
     */
    public function getVerifications(): array
    {
        return $this->defaults['verifications'] ?? [];
    }

    public function isAutoGenerateCanonical(): bool
    {
        return $this->defaults['auto_canonical'] ?? false;
    }

    public function isNoIndex(): bool
    {
        return $this->defaults['no_index'] ?? false;
    }

    public function isAdultContent(): bool
    {
        return $this->defaults['is_adult_content'] ?? false;
    }

    /**
     * @phpstan-assert array{
     *      title?: string|array<string, string>,
     *      title_separator?: string,
     *      description?: string|array<string, string>,
     *      auto_canonical?: bool,
     *      meta?: array<string, array<string, string>>,
     *      og?: array<string, array<string, string>>,
     *      twitter?: array<string, array<string, string>>,
     *      verifications?: array<string, string>,
     *      no_index?: bool,
     *      is_adult_content?: bool
     *  } $defaults
     * @param array<string, array<string, string>|string|bool> $defaults
     */
    private function assertDefaultsArray(array $defaults): void
    {
        if (
            isset($defaults['title'])
            && !\is_array($defaults['title'])
            && !\is_string($defaults['title'])
        ) {
            throw new \InvalidArgumentException('The "title" key in defaults must be an array or a string.');
        }
        if (
            isset($defaults['title_separator'])
            && !\is_string($defaults['title_separator'])
        ) {
            throw new \InvalidArgumentException('The "title_separator" key in defaults must be a string.');
        }
        if (
            isset($this->defaults['description'])
            && !\is_array($this->defaults['description'])
            && !\is_string($this->defaults['description'])
        ) {
            throw new \InvalidArgumentException('The "description" key in defaults must be an array or a string.');
        }
        if (!\is_array($defaults['meta'] ?? [])) {
            throw new \InvalidArgumentException('The "meta" key in defaults must be an array.');
        }
        if (!\is_array($defaults['og'] ?? [])) {
            throw new \InvalidArgumentException('The "og" key in defaults must be an array.');
        }
        if (!\is_array($defaults['twitter'] ?? [])) {
            throw new \InvalidArgumentException('The "twitter" key in defaults must be an array.');
        }
        if (
            isset($defaults['verifications'])
            && !\is_array($defaults['verifications'])
        ) {
            throw new \InvalidArgumentException('The "verifications" key in defaults must be an array.');
        }
    }
}
