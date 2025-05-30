<?php

namespace Gurtok\SeoBundle\Model;

use Gurtok\SeoBundle\Exception\InvalidTagValueException;
use Gurtok\SeoBundle\Exception\UnsupportedTagException;
use Gurtok\SeoBundle\Helper\EnumResolveHelper;
use Gurtok\SeoBundle\Helper\EnumStringValueResolveHelper;

/**
 * @template TInput of mixed
 * @template TValue of string|array<string, string>
 * @implements \ArrayAccess<string, TValue>
 * @implements \IteratorAggregate<string, TValue>
 */
abstract class TagCollection implements TagCollectionInterface, \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var array<string, TValue>
     */
    protected array $tags = [];

    /**
     * @param array<string, string|array<string, string>|mixed> $rawInput
     */
    public function __construct(array $rawInput = [])
    {
        foreach ($rawInput as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param \BackedEnum|string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->tags[$this->getTag($offset)]);
    }

    /**
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param \BackedEnum|string $offset
     * @param TInput $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->tags[$this->normalizeTagName($offset)]);
    }

    /**
     * @return \Traversable<string, TValue>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->tags);
    }

    public function count(): int
    {
        return \count($this->tags);
    }

    /**
     * @param TInput $value
     */
    public function set(\BackedEnum|string $name, mixed $value): static
    {
        $tag = $this->getTag($name);

        $this->assertValue($tag, $value);

        /* @var TValue $normalized */
        $normalized = $this->normalizeValue($tag, $value);

        $this->tags[$tag] = $normalized;

        return $this;
    }

    /**
     * @return TValue|null
     */
    public function get(\BackedEnum|string $name): string|array|null
    {
        $tag = $this->getTag($name);

        return $this->tags[$tag] ?? null;
    }

    /**
     * @return array<string, TValue>
     */
    public function all(): array
    {
        return $this->tags;
    }

    /**
     * @return array<string>
     */
    public function keys(): array
    {
        return array_keys($this->tags);
    }

    public function getTag(\BackedEnum|string $originalName): string
    {
        $name = $this->normalizeTagName($originalName);
        try {
            $tag = EnumResolveHelper::resolve($name, $this->getTagClass(), $this->isAllowedCustomTags());
        } catch (\ValueError) {
            throw new UnsupportedTagException($this->getRealTagName($originalName));
        }

        if (\is_scalar($tag)) {
            $tag = (string) $tag;
        }

        return EnumStringValueResolveHelper::resolve($tag);
    }

    abstract protected function getTagClass(): string;

    abstract protected function isStringValue(string $tag): bool;

    abstract protected function isLocalizedValue(string $tag): bool;

    abstract protected function isAllowedCustomTags(): bool;

    /**
     * @phpstan-assert TInput $value
     */
    protected function assertValue(string $tag, mixed $value): void
    {
        if ($this->isStringValue($tag)) {
            $this->assertString($value, $tag);
        } elseif ($this->isLocalizedValue($tag)) {
            $this->assertLocalizedValue($value, $tag);
        } else {
            throw new UnsupportedTagException($tag);
        }
    }

    /**
     * @phpstan-assert string $value
     * @throws InvalidTagValueException
     */
    protected function assertString(mixed $value, string $tag): void
    {
        if (!\is_string($value)) {
            throw new InvalidTagValueException($tag, $value);
        }
    }

    /**
     * @phpstan-assert array<string, string>|string $value
     * @throws InvalidTagValueException
     */
    protected function assertLocalizedValue(mixed $value, string $tag): void
    {
        if ((!\is_array($value) && !\is_string($value)) || empty($value)) {
            throw new InvalidTagValueException($tag, $value, 'Value must be a non-empty string or a localized array');
        }

        if (\is_string($value)) {
            $value = ['default' => $value];
        }

        foreach ($value as $locale => $val) {
            if (!\is_string($val)) {
                throw new InvalidTagValueException($tag, $val, \sprintf('Value for locale "%s" must be a string', $locale));
            }
        }
    }

    /**
     * @param TInput $value
     * @return TValue
     */
    protected function normalizeValue(string $tag, mixed $value): string|array
    {
        if ($this->isStringValue($tag)) {
            /**
             * @phpstan-ignore-next-line
             */
            return $this->normalizeStringValue($value, $tag);
        }

        if ($this->isLocalizedValue($tag)) {
            /**
             * @phpstan-ignore-next-line
             */
            return $this->normalizeLocalizedValue($value, $tag);
        }

        throw new UnsupportedTagException($tag);
    }

    /**
     * @param TInput $value
     */
    protected function normalizeStringValue(mixed $value, string $tag): string
    {
        $this->assertString($value, $tag);

        return $value;
    }

    /**
     * @param TInput $value
     * @return array<string, string>
     */
    protected function normalizeLocalizedValue(mixed $value, string $tag): array
    {
        $this->assertLocalizedValue($value, $tag);

        if (\is_string($value)) {
            $value = ['default' => $value];
        }

        return $value;
    }

    protected function getRealTagName(\BackedEnum|string $name): string
    {
        return \is_string($name) ? $name : $name->name;
    }

    protected function normalizeTagName(\BackedEnum|string $name): string
    {
        return \is_string($name) ? strtolower($name) : (string) $name->value;
    }
}
