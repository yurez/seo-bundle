<?php

namespace Gurtok\SeoBundle\Tests\Unit\Model;

use Gurtok\SeoBundle\Exception\InvalidTagValueException;
use Gurtok\SeoBundle\Exception\UnsupportedTagException;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\OpenGraphTagCollection;
use PHPUnit\Framework\TestCase;

class OpenGraphTagCollectionTest extends TestCase
{
    /**
     * @dataProvider provideLocalizedTags
     */
    public function testSetLocalizedTag(string $tag): void
    {
        $collection = new OpenGraphTagCollection([]);
        $collection->set($tag, ['en' => 'Hello', 'uk' => 'Привіт']);
        $this->assertSame(['en' => 'Hello', 'uk' => 'Привіт'], $collection->get($tag));
    }

    /**
     * @dataProvider provideStringTags
     */
    public function testSetStringTag(string $tag): void
    {
        $collection = new OpenGraphTagCollection([]);
        $collection->set($tag, 'Static Value');
        $this->assertSame('Static Value', $collection->get($tag));
    }

    /**
     * @dataProvider provideLocalizedTags
     */
    public function testSetInvalidLocalizedTag(string $tag): void
    {
        $this->expectException(InvalidTagValueException::class);
        $collection = new OpenGraphTagCollection([]);
        /**
         * @phpstan-ignore-next-line
         */
        $collection->set($tag, ['en' => 123]); // invalid value
    }

    /**
     * @dataProvider provideStringTags
     */
    public function testSetInvalidStringTag(string $tag): void
    {
        $this->expectException(InvalidTagValueException::class);
        $collection = new OpenGraphTagCollection([]);
        $collection->set($tag, ['en' => 'Not Allowed']); // should be string
    }

    public function testSetUnknownTag(): void
    {
        $this->expectException(UnsupportedTagException::class);
        $collection = new OpenGraphTagCollection([]);
        $collection->set('og:unknown', 'value');
    }

    public function testSetShortTag(): void
    {
        $collection = new OpenGraphTagCollection([]);
        $collection->set('url', 'value');
        $this->assertSame('value', $collection->get('og:url'));
        $this->assertSame('value', $collection->get('url'));
        $this->assertSame('value', $collection->get(OpenGraphTag::URL));
    }

    public function testAllReturnsAllTags(): void
    {
        $collection = new OpenGraphTagCollection([]);
        $collection->set(OpenGraphTag::TITLE, ['en' => 'Title']);
        $collection->set(OpenGraphTag::URL, 'https://example.com');

        $expected = [
            OpenGraphTag::TITLE->value => ['en' => 'Title'],
            OpenGraphTag::URL->value => 'https://example.com',
        ];

        $this->assertSame($expected, $collection->all());
    }

    /**
     * @return array<array<string>>
     */
    public static function provideLocalizedTags(): array
    {
        return [
            [OpenGraphTag::TITLE->value],
            [OpenGraphTag::DESCRIPTION->value],
        ];
    }

    /**
     * @return array<array<string>>
     */
    public static function provideStringTags(): array
    {
        return [
            [OpenGraphTag::IMAGE->value],
            [OpenGraphTag::URL->value],
            [OpenGraphTag::TYPE->value],
            [OpenGraphTag::LOCALE->value],
            [OpenGraphTag::SITE_NAME->value],
        ];
    }
}
