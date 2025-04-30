<?php

namespace Gurtok\SeoBundle\Tests\Helper;

use Gurtok\SeoBundle\Helper\EnumResolveHelper;
use Gurtok\SeoBundle\Model\Enum\FromWithPrefixInterface;
use PHPUnit\Framework\TestCase;

class EnumResolveHelperTest extends TestCase
{
    public function testResolvesBackedEnumDirectly(): void
    {
        $result = EnumResolveHelper::resolve(DummyEnum::ONE, DummyEnum::class);
        $this->assertSame(DummyEnum::ONE, $result);
    }

    public function testResolvesBackedEnumFromValue(): void
    {
        $result = EnumResolveHelper::resolve('one', DummyEnum::class);
        $this->assertSame(DummyEnum::ONE, $result);
    }

    public function testResolvesCustomEnumWithPrefix(): void
    {
        $result = EnumResolveHelper::resolve('prefix:hello', DummyEnumWithPrefix::class);
        $this->assertSame(DummyEnumWithPrefix::HELLO, $result);
    }

    public function testTryFromReturnsOriginalIfAllowed(): void
    {
        $result = EnumResolveHelper::resolve('prefix:unknown', DummyEnumWithPrefix::class, true);
        $this->assertSame('prefix:unknown', $result);
    }

    public function testTryFromReturnsEnumIfValid(): void
    {
        $result = EnumResolveHelper::resolve('prefix:world', DummyEnumWithPrefix::class, true);
        $this->assertSame(DummyEnumWithPrefix::WORLD, $result);
    }

    public function testResolvesOneEnumToAnother(): void
    {
        $result = EnumResolveHelper::resolve(DummyEnum::ONE, DummyEnumWithPrefix::class);
        $this->assertSame(DummyEnumWithPrefix::ONE, $result);
    }

    public function testThrowsOnInvalidEnumClass(): void
    {
        $this->expectException(\ValueError::class);
        EnumResolveHelper::resolve('test', 'stdClass');
    }

    public function testThrowsOnInvalidEnumValue(): void
    {
        $this->expectException(\ValueError::class);
        EnumResolveHelper::resolve('invalid', DummyEnum::class);
    }
}

enum DummyEnum: string
{
    case ONE = 'one';
    case TWO = 'two';
}

enum DummyEnumWithPrefix: string implements FromWithPrefixInterface
{
    case HELLO = 'hello';
    case WORLD = 'world';
    case ONE = 'one';

    public static function fromPrefixed(int|string $value): static
    {
        $unprefixed = str_replace('prefix:', '', (string) $value);

        return self::from($unprefixed);
    }

    public static function tryFromPrefixed(int|string $value): ?static
    {
        $unprefixed = str_replace('prefix:', '', (string) $value);

        return self::tryFrom($unprefixed);
    }
}
