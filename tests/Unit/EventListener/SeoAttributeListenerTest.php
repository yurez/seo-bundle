<?php

namespace Gurtok\SeoBundle\Tests\Unit\EventListener;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\EventListener\SeoAttributeListener;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Service\CanonicalUrlGenerator;
use Gurtok\SeoBundle\Service\SeoManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SeoAttributeListenerTest extends TestCase
{
    private SeoManager&MockObject $seoManager;
    private CanonicalUrlGenerator&MockObject $canonicalUrlGenerator;
    private SeoAttributeListener $listener;

    protected function setUp(): void
    {
        $this->seoManager = $this->createMock(SeoManager::class);
        $this->canonicalUrlGenerator = $this->createMock(CanonicalUrlGenerator::class);

        $this->listener = new SeoAttributeListener(
            seoManager: $this->seoManager,
            canonicalUrlGenerator: $this->canonicalUrlGenerator,
            supportCustomMetaTags: true
        );
    }

    public function testSeoMetaOnMethod(): void
    {
        $request = new Request();

        $controller = [new class {
            #[SeoMeta(
                title: ['en' => 'test.title'],
                description: 'test.description',
                canonical: 'https://example.com',
                meta: ['robots' => 'index,follow'],
                og: ['title' => 'OG Title'],
                twitter: ['card' => 'summary'],
                verifications: ['google-site-verification' => 'code'],
                hreflangs: ['en' => 'https://example.com'],
                noIndex: true,
                isAdultContent: true
            )]
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->seoManager
            ->expects(self::once())
            ->method('setTitle')
            ->with(['en' => 'test.title']);

        $this->seoManager
            ->expects(self::once())
            ->method('setDescription')
            ->with('test.description');

        $this->seoManager
            ->expects(self::once())
            ->method('setCanonical')
            ->with('https://example.com');

        $this->seoManager
            ->expects(self::once())
            ->method('addMeta')
            ->with(MetaTag::ROBOTS, 'index,follow');

        $this->seoManager
            ->expects(self::once())
            ->method('addOpenGraph')
            ->with(OpenGraphTag::TITLE, 'OG Title');

        $this->seoManager
            ->expects(self::once())
            ->method('addTwitter')
            ->with(TwitterCardTag::CARD, 'summary');

        $this->seoManager
            ->expects(self::once())
            ->method('addVerification')
            ->with('google-site-verification', 'code');

        $this->seoManager
            ->expects(self::once())
            ->method('setHreflangs')
            ->with(['en' => 'https://example.com']);

        $this->seoManager
            ->expects(self::once())
            ->method('markAsNoIndex');

        $this->seoManager
            ->expects(self::once())
            ->method('markContentAsAdult');

        $this->listener->__invoke($event);
    }

    public function testWithAutoGenerateCanonical(): void
    {
        $request = new Request();

        $controller = [new class {
            #[SeoMeta(autoGenerateCanonical: true)]
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->canonicalUrlGenerator
            ->expects(self::once())
            ->method('generateFromRequest')
            ->with($request)
            ->willReturn('https://example.com');

        $this->seoManager
            ->expects(self::once())
            ->method('setCanonical')
            ->with('https://example.com');

        $this->listener->__invoke($event);
    }

    public function testWithCustomMetaTags(): void
    {
        $request = new Request();

        $controller = [new class {
            #[SeoMeta(meta: ['custom-tag' => 'custom-value'])]
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->seoManager
            ->expects(self::once())
            ->method('addMeta')
            ->with('custom-tag', 'custom-value');

        $this->listener->__invoke($event);
    }

    public function testWithDisabledCustomMetaTags(): void
    {
        $request = new Request();

        $controller = [new class {
            #[SeoMeta(meta: ['custom-tag' => 'custom-value'])]
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->seoManager
            ->expects(self::never())
            ->method('addMeta');

        $listener = new SeoAttributeListener(
            seoManager: $this->seoManager,
            canonicalUrlGenerator: $this->canonicalUrlGenerator,
            supportCustomMetaTags: false
        );

        $this->expectException(\ValueError::class);

        $listener->__invoke($event);
    }

    public function testWithDisabledDefaults(): void
    {
        $request = new Request();

        $controller = [new class {
            #[SeoMeta(disableDefaults: true)]
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->seoManager
            ->expects(self::once())
            ->method('reset');

        $this->listener->__invoke($event);
    }

    public function testNoSeoMeta(): void
    {
        $request = new Request();

        $controller = [new class {
            public function __invoke(): void
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->seoManager
            ->expects(self::never())
            ->method(self::anything());

        $this->listener->__invoke($event);
    }
}
