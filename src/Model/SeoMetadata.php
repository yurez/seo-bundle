<?php

namespace Gurtok\SeoBundle\Model;

class SeoMetadata
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $canonical = null;
    /**
     * @var array<string, string>
     */
    public array $meta = [];
    /**
     * @var array<string, string>
     */
    public array $og = [];
    /**
     * @var array<string, string>
     */
    public array $twitter = [];
    /**
     * @var array<string, string>
     */
    public array $verifications = [];
    /**
     * @var array<string, string>
     */
    public array $hreflangs = [];
}
