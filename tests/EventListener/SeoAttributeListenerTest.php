<?php

namespace Gurtok\SeoBundle\Tests\EventListener;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\EventListener\SeoAttributeListener;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Service\SeoManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeoAttributeListenerTest extends TestCase
{
    private SeoManager $seoManager;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private SeoAttributeListener $listener;

    protected function setUp(): void
    {
        $this->seoManager = $this->createMock(SeoManager::class);
        $this->requestStack = new RequestStack();
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->listener = new SeoAttributeListener(
            seoManager: $this->seoManager,
            requestStack: $this->requestStack,
            translator: $this->translator,
            defaultLocale: 'en',
            supportCustomMetaTags: true
        );
    }

    public function testSeoMetaOnMethod(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $controller = [new class {
            #[SeoMeta(
                title: 'test.title',
                description: 'test.description',
                canonical: 'https://example.com',
                meta: ['robots' => 'index,follow'],
                og: ['title' => 'OG Title'],
                twitter: ['card' => 'summary'],
                verifications: ['google-site-verification' => 'code'],
                hreflangs: ['en' => 'https://example.com']
            )]
            public function __invoke()
            {
            }
        }, '__invoke'];

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->translator
            ->method('trans')
            ->willReturnCallback(fn ($str) => $str); // Під час тесту не перекладаємо реально

        $this->seoManager
            ->expects(self::once())
            ->method('setTitle')
            ->with('test.title');

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
            ->method('addOg')
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

        $this->listener->__invoke($event);
    }

    public function testNoSeoMeta(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $controller = [new class {
            public function __invoke()
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
