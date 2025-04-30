<?php

namespace Gurtok\SeoBundle\Tests\Model\Enum\Traits;

use Gurtok\SeoBundle\Model\Enum\FromWithPrefixInterface;
use Gurtok\SeoBundle\Model\Enum\Traits\FromWithPrefixTrait;
use PHPUnit\Framework\TestCase;

class FromWithPrefixTraitTest extends TestCase
{
    public function testFromPrefixedReturnsCorrectCase(): void
    {
        $enum = TestPrefixedEnum::fromPrefixed('alpha');
        $this->assertSame(TestPrefixedEnum::ALPHA, $enum);

        $enum2 = TestPrefixedEnum::fromPrefixed('prefix:beta');
        $this->assertSame(TestPrefixedEnum::BETA, $enum2);
    }

    public function testTryFromPrefixedReturnsCorrectCase(): void
    {
        $enum = TestPrefixedEnum::tryFromPrefixed('prefix:alpha');
        $this->assertSame(TestPrefixedEnum::ALPHA, $enum);

        $enum2 = TestPrefixedEnum::tryFromPrefixed('beta');
        $this->assertSame(TestPrefixedEnum::BETA, $enum2);
    }

    public function testTryFromPrefixedReturnsNullOnInvalid(): void
    {
        $this->assertNull(TestPrefixedEnum::tryFromPrefixed('unknown'));
    }

    public function testFromPrefixedThrowsOnInvalid(): void
    {
        $this->expectException(\ValueError::class);
        TestPrefixedEnum::fromPrefixed('unknown');
    }
}

enum TestPrefixedEnum: string implements FromWithPrefixInterface
{
    use FromWithPrefixTrait;

    case ALPHA = 'prefix:alpha';
    case BETA = 'prefix:beta';

    protected static function prefix(): string
    {
        return 'prefix:';
    }
}
