<?php

namespace Gurtok\SeoBundle\Tests\Functional;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Tests\Fixtures\SeoServicesTestHelper;
use Gurtok\SeoBundle\Tests\Fixtures\TestKernel;
use PHPUnit\Framework\TestCase;

class SeoDefaultsProviderTest extends TestCase
{
    public function testDefaultsWork(): void
    {
        $kernel = new TestKernel('default', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var SeoServicesTestHelper $service */
        $service = $container->get(SeoServicesTestHelper::class);
        $provider = $service->getSeoDefaultsProvider();

        $this->assertSame(
            [
                'en' => 'Default title',
                'uk' => 'Заголовок українською',
                'es' => 'Título en español',
            ],
            $provider->getTitle()
        );
        $this->assertSame(
            [
                'uk' => 'Опис українською',
                'default' => 'Default description from Default',
            ],
            $provider->getDescription()
        );
        $this->assertSame(
            [
                'en' => 'seo, web, club',
                'uk' => 'seo, веб, гурток',
            ],
            $provider->getMeta('keywords')
        );
        $this->assertSame(
            [
                'en' => 'Default og title',
                'uk' => 'og заголовок українською',
            ],
            $provider->getOpenGraph(OpenGraphTag::TITLE)
        );
        $this->assertSame(
            [
                'en' => 'Default og description',
                'uk' => 'og опис українською',
            ],
            $provider->getOpenGraph(OpenGraphTag::DESCRIPTION)
        );
        $this->assertSame('/images/default.png', $provider->getOpenGraph(OpenGraphTag::IMAGE));
        $this->assertSame('https://example.com/canonical', $provider->getOpenGraph(OpenGraphTag::URL));
        $this->assertSame(
            [
                'default' => 'Default twitter title like string',
            ],
            $provider->getTwitter(TwitterCardTag::TITLE)
        );
        $this->assertSame(
            [
                'en' => 'Default twitter description',
                'uk' => 'twitter опис українською',
            ],
            $provider->getTwitter(TwitterCardTag::DESCRIPTION)
        );
        $this->assertSame('summary', $provider->getTwitter(TwitterCardTag::CARD));
        $this->assertTrue($provider->isAutoGenerateCanonical());
        $this->assertFalse($provider->isNoIndex());
        $this->assertFalse($provider->isAdultContent());
    }

    public function testDefaultsWithSimpleValue(): void
    {
        $kernel = new TestKernel('simple', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var SeoServicesTestHelper $service */
        $service = $container->get(SeoServicesTestHelper::class);
        $provider = $service->getSeoDefaultsProvider();

        $this->assertSame(
            ['default' => 'Simple title'],
            $provider->getTitle()
        );

        $this->assertSame(
            ['default' => 'Simple description'],
            $provider->getDescription()
        );

        $this->assertSame(
            [
                'default' => 'Simple meta title',
            ],
            $provider->getMeta(MetaTag::TITLE)
        );

        $this->assertSame(
            [
                'default' => 'Simple Author',
            ],
            $provider->getMeta(MetaTag::AUTHOR)
        );

        $this->assertSame(
            'width=device-width, initial-scale=1.0',
            $provider->getMeta(MetaTag::VIEWPORT)
        );

        $this->assertSame(
            [
                'default' => 'Simple og title',
            ],
            $provider->getOpenGraph(OpenGraphTag::TITLE)
        );

        $this->assertSame(
            [
                'default' => 'Simple og description',
            ],
            $provider->getOpenGraph(OpenGraphTag::DESCRIPTION)
        );

        $this->assertSame(
            [
                'default' => 'Simple twitter title',
            ],
            $provider->getTwitter(TwitterCardTag::TITLE)
        );

        $this->assertSame(
            [
                'default' => 'Simple twitter description',
            ],
            $provider->getTwitter(TwitterCardTag::DESCRIPTION)
        );

        $this->assertSame('summary_large_image', $provider->getTwitter('card'));
    }

    public function testDefaultsWithCustomMetaTags(): void
    {
        $kernel = new TestKernel('custom', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var SeoServicesTestHelper $service */
        $service = $container->get(SeoServicesTestHelper::class);
        $provider = $service->getSeoDefaultsProvider();

        $this->assertSame(
            'Custom Meta Value',
            $provider->getMeta('custom-meta')
        );
    }
}
