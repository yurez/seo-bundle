<?php

namespace Gurtok\SeoBundle\Model;

interface TagCollectionInterface
{
    public function set(\BackedEnum|string $name, mixed $value): static;

    /**
     * @return string|array<string, string>|null
     */
    public function get(\BackedEnum|string $name): string|array|null;

    /**
     * @return array<string, string|array<string, string>>
     */
    public function all(): array;
}
