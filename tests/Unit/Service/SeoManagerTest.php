<?php

namespace Gurtok\SeoBundle\Tests\Unit\Service;

use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
use Gurtok\SeoBundle\Service\ImageUrlResolverInterface;
use Gurtok\SeoBundle\Service\LocalizedResolverInterface;
use Gurtok\SeoBundle\Service\SeoManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SeoManagerTest extends TestCase
{
    private LocalizedResolverInterface&MockObject $localizedResolver;
    private ImageUrlResolverInterface&MockObject $imageUrlResolver;

    protected function setUp(): void
    {
        $this->localizedResolver = $this->createMock(LocalizedResolverInterface::class);
        $this->localizedResolver
            ->method('resolveValue')
            ->willReturnCallback(function (array|string|null $value): ?string {
                if (\is_array($value)) {
                    return $value['en'] ?? reset($value);
                }

                return $value;
            });
        $this->imageUrlResolver = $this->createMock(ImageUrlResolverInterface::class);
        $this->imageUrlResolver
            ->method('resolve')
            ->willReturnCallback(function (string $path): string {
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }

                return 'https://example.com/'.ltrim($path, '/');
            });
    }

    private function createSeoManager($supportCustomMetaTags = false): SeoManager
    {
        return new SeoManager(
            localizedResolver: $this->localizedResolver,
            imageUrlResolver: $this->imageUrlResolver,
            supportCustomMetaTags: $supportCustomMetaTags,
        );
    }

    public function testSetAndGetTitle()
    {
        $manager = $this->createSeoManager();
        $manager->setTitle('My Title');
        $this->assertSame('My Title', $manager->getTitle());
    }

    public function testFullTitleWithPrefix()
    {
        $manager = $this->createSeoManager();
        $manager->setTitle('Page')->setTitlePrefix('Site');
        $this->assertSame('Site - Page', $manager->getFullTitle());
    }

    public function testMeta()
    {
        $manager = $this->createSeoManager();
        $manager->addMeta(MetaTag::DESCRIPTION, 'meta desc');
        $this->assertTrue($manager->hasMeta(MetaTag::DESCRIPTION));
        $this->assertSame('meta desc', $manager->getMeta(MetaTag::DESCRIPTION));
    }

    public function testOpenGraphImageResolution()
    {
        $manager = $this->createSeoManager();
        $manager->addOpenGraph(OpenGraphTag::IMAGE, '/img/logo.jpg');
        $this->assertSame('https://example.com/img/logo.jpg', $manager->getOpenGraph(OpenGraphTag::IMAGE));
    }

    public function testTwitterImageResolution()
    {
        $manager = $this->createSeoManager();
        $manager->addTwitter(TwitterCardTag::IMAGE, '/img/logo.jpg');
        $this->assertSame('https://example.com/img/logo.jpg', $manager->getTwitter(TwitterCardTag::IMAGE));
    }

    public function testTwitterCardType()
    {
        $manager = $this->createSeoManager();
        $manager->addTwitter(TwitterCardTag::CARD, TwitterCardType::SUMMARY);
        $this->assertSame('summary', $manager->getTwitter(TwitterCardTag::CARD));
    }

    public function testMarkContentFlags()
    {
        $manager = $this->createSeoManager();
        $this->assertFalse($manager->isAdultContent());
        $this->assertFalse($manager->isNoIndex());

        $manager->markContentAsAdult()->markAsNoIndex();

        $this->assertTrue($manager->isAdultContent());
        $this->assertTrue($manager->isNoIndex());
    }

    public function testResetBehavior()
    {
        $manager = $this->createSeoManager();
        $manager->setTitle('Title');
        $manager->reset();
        $this->assertNull($manager->getTitle());
    }

    public function testResetThrowsAfterRender()
    {
        $this->expectException(\RuntimeException::class);

        $manager = $this->createSeoManager();
        $manager->markAsRendered();
        $manager->reset(); // should throw
    }

    public function testCustomMetaTag()
    {
        $manager = $this->createSeoManager(supportCustomMetaTags: true);
        $manager->addMeta('x-custom', 'value');
        $this->assertSame('value', $manager->getMeta('x-custom'));
    }

    public function testDisableCustomMetaTag()
    {
        $this->expectException(\ValueError::class);
        $manager = $this->createSeoManager(supportCustomMetaTags: false);
        $manager->addMeta('x-custom', 'value');
    }
}
