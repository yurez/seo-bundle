<?php

namespace Gurtok\SeoBundle\Tests\Unit\Model;

use Gurtok\SeoBundle\Exception\InvalidTagValueException;
use Gurtok\SeoBundle\Exception\UnsupportedTagException;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\MetaTagCollection;
use PHPUnit\Framework\TestCase;

class MetaTagCollectionTest extends TestCase
{
    /**
     * @dataProvider provideLocalizedTags
     */
    public function testAcceptsLocalizedTags(string $tag): void
    {
        $collection = new MetaTagCollection([
            $tag => ['en' => 'value', 'uk' => 'текст'],
        ]);

        $this->assertSame(['en' => 'value', 'uk' => 'текст'], $collection->get($tag));
    }

    /**
     * @dataProvider provideLocalizedTags
     */
    public function testAcceptsLocalizedTagsWithString(string $tag): void
    {
        $collection = new MetaTagCollection([
            $tag => 'meta-value',
        ]);

        $this->assertSame(['default' => 'meta-value'], $collection->get($tag));
    }

    /**
     * @dataProvider provideStringTags
     */
    public function testAcceptsStringTags(string $tag): void
    {
        $collection = new MetaTagCollection([
            $tag => 'meta-value',
        ]);

        $this->assertSame('meta-value', $collection->get($tag));
    }

    /**
     * @dataProvider provideLocalizedTags
     */
    public function testRejectsInvalidLocalizedTag(string $tag): void
    {
        $this->expectException(InvalidTagValueException::class);

        new MetaTagCollection([
            $tag => ['uk' => 123], // not string
        ]);
    }

    /**
     * @dataProvider provideStringTags
     */
    public function testRejectsInvalidStringTag(string $tag): void
    {
        $this->expectException(InvalidTagValueException::class);

        new MetaTagCollection([
            $tag => ['uk' => 'not allowed'], // should be string
        ]);
    }

    public function testRejectsUnknownTag(): void
    {
        $this->expectException(UnsupportedTagException::class);

        new MetaTagCollection([
            'not-real-tag' => 'value',
        ]);
    }

    public function testSetLocalizedTag(): void
    {
        $collection = new MetaTagCollection([]);
        $collection->set(MetaTag::TITLE, ['en' => 'Hello', 'uk' => 'Привіт']);
        $this->assertSame(['en' => 'Hello', 'uk' => 'Привіт'], $collection->get(MetaTag::TITLE));
    }

    public function testSetStringTag(): void
    {
        $collection = new MetaTagCollection([]);
        $collection->set(MetaTag::THEME_COLOR, 'black');
        $this->assertSame('black', $collection->get(MetaTag::THEME_COLOR));
    }

    public function testSetInvalidLocalizedTag(): void
    {
        $this->expectException(InvalidTagValueException::class);

        $collection = new MetaTagCollection([]);
        $collection->set(MetaTag::TITLE, ['en' => 123]); // invalid value
    }

    public function testSetInvalidStringTag(): void
    {
        $this->expectException(InvalidTagValueException::class);
        $collection = new MetaTagCollection([]);
        $collection->set(MetaTag::THEME_COLOR, ['en' => 'UTF-8']); // should be string
    }

    public function testAllReturnsAllTags(): void
    {
        $collection = new MetaTagCollection([]);
        $collection->set(MetaTag::TITLE, ['en' => 'Title']);
        $collection->set(MetaTag::THEME_COLOR, 'red');

        $expected = [
            MetaTag::TITLE->value => ['en' => 'Title'],
            MetaTag::THEME_COLOR->value => 'red',
        ];

        $this->assertSame($expected, $collection->all());
    }

    /**
     * @return array<array<string>>
     */
    public static function provideLocalizedTags(): array
    {
        return [
            [MetaTag::TITLE->value],
            [MetaTag::DESCRIPTION->value],
            [MetaTag::AUTHOR->value],
            [MetaTag::KEYWORDS->value],
        ];
    }

    /**
     * @return array<array<string>>
     */
    public static function provideStringTags(): array
    {
        return [
            [MetaTag::ROBOTS->value],
            [MetaTag::VIEWPORT->value],
            [MetaTag::THEME_COLOR->value],
            [MetaTag::GOOGLE->value],
            [MetaTag::GOOGLEBOT->value],
            [MetaTag::RATING->value],
        ];
    }
}
