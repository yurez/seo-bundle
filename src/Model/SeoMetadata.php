<?php

namespace Gurtok\SeoBundle\Model;

/**
 * @property string|array<string, string>|null $title
 * @property string|array<string, string>|null $description
 */
class SeoMetadata
{
    public MetaTagCollection $meta;
    public OpenGraphTagCollection $og;
    public TwitterTagCollection $twitter;

    public ?string $titleSeparator = null;
    /**
     * @var string|array<string, string>|null
     */
    public string|array|null $titlePrefix = null;

    public ?string $canonical = null;
    /**
     * Key is verification type (e.g., "google-site-verification"), value is verification code or URL
     * @var array<string, string>
     */
    public array $verifications = [];
    /**
     * Key is language code, value is URL
     * @var array<string, string>
     */
    public array $hreflangs = [];

    /**
     * @var string|array<string, string>|null
     */
    protected string|array|null $title = null;
    /**
     * @var string|array<string, string>|null
     */
    protected string|array|null $description = null;

    public function __construct(
        bool $supportCustomMetaTags = false,
        ?MetaTagCollection $meta = null,
        ?OpenGraphTagCollection $og = null,
        ?TwitterTagCollection $twitter = null,
    ) {
        $this->meta = $meta ?? new MetaTagCollection(allowedCustomTags: $supportCustomMetaTags);
        $this->og = $og ?? new OpenGraphTagCollection();
        $this->twitter = $twitter ?? new TwitterTagCollection();
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name) && null !== $this->{$name}) {
            return $this->{$name};
        }

        return $this->meta[$name] ?? $this->og[$name] ?? $this->twitter[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            throw new \InvalidArgumentException(\sprintf('Property "%s" does not exist.', $name));
        }
    }
}
