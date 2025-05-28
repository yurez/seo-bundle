<?php

namespace Gurtok\SeoBundle\Tests\Unit\Model;

use Gurtok\SeoBundle\Exception\InvalidTagValueException;
use Gurtok\SeoBundle\Exception\UnsupportedTagException;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
use Gurtok\SeoBundle\Model\TwitterTagCollection;
use PHPUnit\Framework\TestCase;

class TwitterTagCollectionTest extends TestCase
{
    /**
     * @dataProvider provideValidCardValues
     */
    public function testSetCardWithValidValues(string|TwitterCardType $value, string $expected): void
    {
        $collection = new TwitterTagCollection([]);
        $collection->set(TwitterCardTag::CARD, $value);
        $this->assertSame($expected, $collection->get(TwitterCardTag::CARD));
    }

    /**
     * @return array<string, array{0: string|TwitterCardType, 1: string}>
     */
    public static function provideValidCardValues(): array
    {
        return [
            'enum summary' => [TwitterCardType::SUMMARY, 'summary'],
            'enum summary_large_image' => [TwitterCardType::SUMMARY_LARGE_IMAGE, 'summary_large_image'],
            'string summary' => ['summary', 'summary'],
            'string summary_large_image' => ['summary_large_image', 'summary_large_image'],
        ];
    }

    /**
     * @dataProvider provideInvalidCardValues
     */
    public function testSetCardWithInvalidValues(mixed $value): void
    {
        $this->expectException(InvalidTagValueException::class);

        $collection = new TwitterTagCollection([]);
        $collection->set(TwitterCardTag::CARD, $value);
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public static function provideInvalidCardValues(): array
    {
        return [
            'invalid string' => ['invalid_card_type'],
            'array value' => [['not', 'a', 'string']],
            'integer value' => [123],
            'null value' => [null],
        ];
    }

    /**
     * @dataProvider provideLocalizedTags
     */
    public function testSetLocalizedTag(string $tag): void
    {
        $collection = new TwitterTagCollection([]);
        $collection->set($tag, ['en' => 'Hello', 'uk' => 'Привіт']);
        $this->assertSame(['en' => 'Hello', 'uk' => 'Привіт'], $collection->get($tag));
    }

    /**
     * @return array<array<string>>
     */
    public static function provideLocalizedTags(): array
    {
        return [
            [TwitterCardTag::TITLE->value],
            [TwitterCardTag::DESCRIPTION->value],
        ];
    }

    /**
     * @dataProvider provideStringTags
     */
    public function testSetStringTag(string $tag): void
    {
        $collection = new TwitterTagCollection([]);
        $collection->set($tag, 'Static Value');
        $this->assertSame('Static Value', $collection->get($tag));
    }

    /**
     * @return array<array<string>>
     */
    public static function provideStringTags(): array
    {
        return [
            [TwitterCardTag::IMAGE->value],
        ];
    }

    public function testSetUnknownTag(): void
    {
        $this->expectException(UnsupportedTagException::class);
        $collection = new TwitterTagCollection([]);
        $collection->set('twitter:unknown', 'value');
    }

    public function testSetShortTag(): void
    {
        $collection = new TwitterTagCollection([]);
        $collection->set('card', TwitterCardType::SUMMARY);
        $this->assertSame('summary', $collection->get('twitter:card'));
        $this->assertSame('summary', $collection->get('card'));
        $this->assertSame('summary', $collection->get(TwitterCardTag::CARD));
    }
}
