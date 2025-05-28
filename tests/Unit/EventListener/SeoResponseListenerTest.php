<?php

namespace Gurtok\SeoBundle\Tests\Unit\EventListener;

use Gurtok\SeoBundle\EventListener\SeoResponseListener;
use Gurtok\SeoBundle\Service\SeoManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

class SeoResponseListenerTest extends TestCase
{
    private Environment&MockObject $twig;
    private SeoManager&MockObject $seoManager;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->seoManager = $this->createMock(SeoManager::class);
    }

    public function testInjectsSeoIntoHtmlResponse(): void
    {
        $listener = new SeoResponseListener($this->seoManager, $this->twig, true, []);

        $response = new Response('<html><head></head><body>Hello</body></html>', 200, [
            'Content-Type' => 'text/html',
        ]);

        $request = new Request();
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->seoManager
            ->expects(self::atLeastOnce())
            ->method('isRendered')
            ->willReturn(false);
        $this->twig
            ->expects(self::once())
            ->method('render')
            ->willReturn('<meta name="description" content="Test SEO">');

        $listener->__invoke($event);

        $this->assertEquals(
            '<html><head><meta name="description" content="Test SEO"></head><body>Hello</body></html>',
            $response->getContent()
        );
    }

    public function testDoesNothingForNonHtmlResponse(): void
    {
        $listener = new SeoResponseListener($this->seoManager, $this->twig, true, []);

        $response = new Response('{"message":"ok"}', 200, [
            'Content-Type' => 'application/json',
        ]);

        $request = new Request();
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $listener->__invoke($event);

        $this->assertSame('{"message":"ok"}', $response->getContent());
    }

    public function testDoesNothingIfSeoAlreadyRendered(): void
    {
        $listener = new SeoResponseListener($this->seoManager, $this->twig, true, []);

        $response = new Response('<html><head></head><body>Hello</body></html>', 200, [
            'Content-Type' => 'text/html',
        ]);

        $request = new Request();
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->seoManager
            ->method('isRendered')
            ->willReturn(true);
        $this->twig
            ->method('render')
            ->willReturn('<meta name="description" content="Test SEO">');

        $listener->__invoke($event);

        $this->assertStringNotContainsString('<meta', (string) $response->getContent());
    }

    public function testDoesNothingIfPathIsExcluded(): void
    {
        $listener = new SeoResponseListener($this->seoManager, $this->twig, true, ['/admin']);

        $response = new Response('<html><head></head><body>Hello</body></html>', 200, [
            'Content-Type' => 'text/html',
        ]);

        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/admin/dashboard']);
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->seoManager
            ->method('isRendered')
            ->willReturn(false);
        $this->twig
            ->method('render')
            ->willReturn('<meta name="description" content="Test SEO">');

        $listener->__invoke($event);

        $this->assertStringNotContainsString('<meta', (string) $response->getContent());
    }

    public function testDoesNothingIfNoHeadTag(): void
    {
        $listener = new SeoResponseListener($this->seoManager, $this->twig, true, []);

        $response = new Response('<html><body>No head here</body></html>', 200, [
            'Content-Type' => 'text/html',
        ]);

        $request = new Request();
        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response
        );

        $this->seoManager
            ->method('isRendered')
            ->willReturn(false);
        $this->twig
            ->method('render')
            ->willReturn('<meta name="description" content="Test SEO">');

        $listener->__invoke($event);

        $this->assertStringNotContainsString('<meta', (string) $response->getContent());
    }
}
