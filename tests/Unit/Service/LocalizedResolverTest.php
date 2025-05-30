<?php

namespace Gurtok\SeoBundle\Tests\Unit\Service;

use Gurtok\SeoBundle\Service\LocalizedResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocalizedResolverTest extends TestCase
{
    public function testResolveWithStringValue(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('trans')
            ->with('main.title')
            ->willReturn('Translated Title');

        $resolver = new LocalizedResolver(new RequestStack(), $translator);
        $this->assertEquals('Translated Title', $resolver->resolveValue('main.title'));
    }

    public function testResolveWithLocalizedArray(): void
    {
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setLocale('uk');
        $requestStack->push($request);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->never())
            ->method('trans');

        $resolver = new LocalizedResolver($requestStack, $translator, 'en');
        $value = ['en' => 'English Title', 'uk' => 'Український заголовок'];

        $this->assertEquals('Український заголовок', $resolver->resolveValue($value));
    }

    public function testResolveWithDefaultFallback(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('trans')
            ->with('default.title')
            ->willReturn('Default Translated');

        $resolver = new LocalizedResolver(new RequestStack(), $translator, 'en');
        $value = ['default' => 'default.title'];

        $this->assertEquals('Default Translated', $resolver->resolveValue($value));
    }

    public function testResolveWithDefaultLocalFallback(): void
    {
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setLocale('uk');
        $requestStack->push($request);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->never())
            ->method('trans');

        $resolver = new LocalizedResolver($requestStack, $translator, 'fr');
        $value = ['en' => 'Default Title', 'fr' => 'La Titre par Défaut'];

        $this->assertEquals('La Titre par Défaut', $resolver->resolveValue($value));
    }

    public function testResolveWithOnlyOneItem(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('trans')
            ->with('lonely.value')
            ->willReturn('Lonely Translated');

        $resolver = new LocalizedResolver(new RequestStack(), $translator, 'en');
        $value = ['lonely.value'];

        $this->assertEquals('Lonely Translated', $resolver->resolveValue($value));
    }

    public function testResolveWithNoTranslation(): void
    {
        $resolver = new LocalizedResolver(new RequestStack(), null, 'en');
        $value = ['en' => 'No Translator Title'];

        $this->assertEquals('No Translator Title', $resolver->resolveValue($value));
    }

    public function testResolveWithEmptyArray(): void
    {
        $resolver = new LocalizedResolver(new RequestStack(), null);
        $this->assertNull($resolver->resolveValue([]));
    }

    public function testResolveWithManualLocale(): void
    {
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setLocale('en');
        $requestStack->push($request);

        $resolver = new LocalizedResolver($requestStack, null);
        $resolver->setLocale('uk');
        $this->assertEquals('UA', $resolver->resolveValue(['uk' => 'UA', 'en' => 'EN']));
    }
}
