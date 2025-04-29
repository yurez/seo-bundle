<?php

namespace Gurtok\SeoBundle\Model;

class SeoMetadata
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $canonical = null;
    public array $meta = [];
    public array $og = [];
    public array $twitter = [];
    public array $verifications = [];
    public array $hreflangs = [];
}
