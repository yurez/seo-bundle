<?php

namespace Gurtok\SeoBundle\Tests\Unit\Service;

use Gurtok\SeoBundle\Helper\EnumResolveHelper;
use Gurtok\SeoBundle\Helper\EnumStringValueResolveHelper;
use Gurtok\SeoBundle\Model\Enum\MetaTag;
use Gurtok\SeoBundle\Model\Enum\OpenGraphTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardTag;
use Gurtok\SeoBundle\Model\Enum\TwitterCardType;
use Gurtok\SeoBundle\Model\MetaTagCollection;
use Gurtok\SeoBundle\Model\OpenGraphTagCollection;
use Gurtok\SeoBundle\Model\TwitterTagCollection;
use Gurtok\SeoBundle\Service\SeoManager;
use Gurtok\SeoBundle\Service\SeoTagHtmlBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SeoTagHtmlBuilderTest extends TestCase
{
    private SeoTagHtmlBuilder $builder;
    private SeoManager&MockObject $seoManager;

    protected function setUp(): void
    {
        $this->seoManager = $this->createMock(SeoManager::class);
        $this->seoManager
            ->method('getMetaTag')
            ->willReturnCallback(function (MetaTag|string $tag) {
                return EnumStringValueResolveHelper::resolve($tag);
            });
        $this->seoManager
            ->method('getOpenGraphTag')
            ->willReturnCallback(function (OpenGraphTag|string $tag) {
                /**
                 * @phpstan-ignore-next-line
                 */
                return EnumResolveHelper::resolve($tag, OpenGraphTag::class)->value;
            });
        $this->seoManager
            ->method('getTwitterTag')
            ->willReturnCallback(function (TwitterCardTag|string $tag) {
                /**
                 * @phpstan-ignore-next-line
                 */
                return EnumResolveHelper::resolve($tag, TwitterCardTag::class)->value;
            });

        $this->builder = new SeoTagHtmlBuilder(
            $this->seoManager,
            adultContentMetaTags: [
                'rating' => ['rating', 'adult'],
            ]
        );
    }

    public function testBuildTitle(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn('My Title');
        $result = $this->builder->buildTitle();

        $this->assertSame('<title>My Title</title>', $result);
    }

    public function testBuildTitleWithEmptyValueOnSkipEmpty(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn(null);

        $result = $this->builder->buildTitle(skipEmpty: true);

        $this->assertEmpty($result);
    }

    public function testBuildTitleWithEmptyValueOnNotSkipEmpty(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn(null);

        $result = $this->builder->buildTitle(skipEmpty: false);

        $this->assertSame('<title></title>', $result);
    }

    public function testBuildDescription(): void
    {
        $this->seoManager->method('getDescription')->willReturn('My Description');

        $result = $this->builder->buildDescription();

        $this->assertSame('<meta name="description" content="My Description">', $result);
    }

    public function testBuildDescriptionWithEmptyValueOnSkipEmpty(): void
    {
        $this->seoManager->method('getDescription')->willReturn(null);

        $result = $this->builder->buildDescription(skipEmpty: true);

        $this->assertEmpty($result);
    }

    public function testBuildDescriptionWithEmptyValueOnNotSkipEmpty(): void
    {
        $this->seoManager->method('getDescription')->willReturn(null);

        $result = $this->builder->buildDescription(skipEmpty: false);

        $this->assertSame('<meta name="description" content="">', $result);
    }

    public function testBuildMetaTagWithExistingTag(): void
    {
        $this->seoManager
            ->method('getMeta')
            ->with(MetaTag::KEYWORDS)
            ->willReturn('keyword1, keyword2');

        $result = $this->builder->buildMetaTag(MetaTag::KEYWORDS);

        $this->assertSame('<meta name="keywords" content="keyword1, keyword2">', $result);
    }

    public function testBuildMetaTagWithNonExistingTagOnSkipEmpty(): void
    {
        $this->seoManager
            ->method('getMeta')
            ->with(MetaTag::KEYWORDS)
            ->willReturn(null);

        $result = $this->builder->buildMetaTag(MetaTag::KEYWORDS, skipEmpty: true);

        $this->assertEmpty($result);
    }

    public function testBuildMetaTagWithNonExistingTagOnNotSkipEmpty(): void
    {
        $this->seoManager
            ->method('getMeta')
            ->with(MetaTag::KEYWORDS)
            ->willReturn(null);

        $result = $this->builder->buildMetaTag(MetaTag::KEYWORDS, skipEmpty: false);

        $this->assertSame('<meta name="keywords" content="">', $result);
    }

    public function testNoIndexMetaTagIfTrue(): void
    {
        $this->seoManager->method('isNoIndex')->willReturn(true);

        $result = $this->builder->buildNoIndex();

        $this->assertSame('<meta name="robots" content="noindex, nofollow">', $result);
    }

    public function testNoIndexMetaTagIfFalse(): void
    {
        $this->seoManager->method('isNoIndex')->willReturn(false);

        $result = $this->builder->buildNoIndex();

        $this->assertEmpty($result);
    }

    public function testAdultContentMetaTagIfTrue(): void
    {
        $this->seoManager->method('isAdultContent')->willReturn(true);

        $result = $this->builder->buildAdultContent();

        $this->assertSame('<meta name="rating" content="adult">', $result);
    }

    public function testAdultContentMetaTagIfFalse(): void
    {
        $this->seoManager->method('isAdultContent')->willReturn(false);

        $result = $this->builder->buildAdultContent();

        $this->assertEmpty($result);
    }

    public function testBuildAllMetaTagsWithIncludeTitleOnSkipEmptyIfNoIndexFalse(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn('My Title');
        $this->seoManager->method('getDescription')->willReturn('My Description');
        $this->seoManager->method('getMetaCollection')->willReturn(
            new MetaTagCollection([
                MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                MetaTag::DESCRIPTION->value => 'My Description',
                MetaTag::VIEWPORT->value => '',
                MetaTag::ROBOTS->value => 'index, follow',
            ])
        );
        $this->seoManager->method('getMeta')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        MetaTag::DESCRIPTION->value => 'My Description',
                        MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                        MetaTag::ROBOTS->value => 'index, follow',
                        MetaTag::VIEWPORT->value => '',
                        default => null,
                    };
                }
            );

        $this->seoManager->method('isNoIndex')->willReturn(false);

        $result = $this->builder->buildAllMetaTags();

        $expected = [
            '<title>My Title</title>',
            '<meta name="description" content="My Description">',
            '<meta name="keywords" content="keyword1, keyword2">',
            '<meta name="robots" content="index, follow">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildAllMetaTagsNotIncludeTitleOnNonSkipEmptyIfNoIndexTrue(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn('My Title');
        $this->seoManager->method('getDescription')->willReturn(null);

        $this->seoManager->method('getMetaCollection')->willReturn(
            new MetaTagCollection([
                MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                MetaTag::ROBOTS->value => 'index, follow',
                MetaTag::VIEWPORT->value => '',
            ])
        );
        $this->seoManager->method('getMeta')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                        MetaTag::ROBOTS->value => 'index, follow',
                        MetaTag::VIEWPORT->value => '',
                        default => null,
                    };
                }
            );

        $this->seoManager->method('isNoIndex')->willReturn(true);

        $result = $this->builder->buildAllMetaTags(includeTitle: false, skipEmpty: false);

        $expected = [
            '<meta name="description" content="">',
            '<meta name="robots" content="noindex, nofollow">',
            '<meta name="keywords" content="keyword1, keyword2">',
            '<meta name="viewport" content="">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildOpenGraphTag(): void
    {
        $this->seoManager->method('getOpenGraph')
            ->with(OpenGraphTag::TITLE)
            ->willReturn('Open Graph Title');

        $result = $this->builder->buildOpenGraphTag(OpenGraphTag::TITLE);

        $expected = [
            '<meta property="og:title" content="Open Graph Title">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildAllOpenGraphTags(): void
    {
        $this->seoManager->method('getOpenGraphCollection')->willReturn(
            new OpenGraphTagCollection([
                OpenGraphTag::TITLE->value => 'keyword1, keyword2',
                OpenGraphTag::TYPE->value => 'index, follow',
            ])
        );
        $this->seoManager->method('getOpenGraph')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        OpenGraphTag::TITLE->value => 'open graph title',
                        OpenGraphTag::TYPE->value => 'website',
                        default => null,
                    };
                }
            );

        $result = $this->builder->buildAllOpenGraphTags();

        $expected = [
            '<meta property="og:title" content="open graph title">',
            '<meta property="og:type" content="website">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildTwitterTag(): void
    {
        $this->seoManager->method('getTwitter')
            ->with('card')
            ->willReturn('summary');

        $result = $this->builder->buildTwitterTag('card');

        $expected = [
            '<meta name="twitter:card" content="summary">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildAllTwitterTags(): void
    {
        $this->seoManager->method('getTwitterCollection')->willReturn(
            new TwitterTagCollection([
                TwitterCardTag::TITLE->value => 'Title for Twitter',
                TwitterCardTag::IMAGE->value => 'https://example.com/image.jpg',
            ])
        );
        $this->seoManager->method('getTwitter')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        TwitterCardTag::TITLE->value => 'Title for Twitter',
                        TwitterCardTag::IMAGE->value => 'https://example.com/image.jpg',
                        default => null,
                    };
                }
            );

        $result = $this->builder->buildAllTwitterTags();

        $expected = [
            '<meta name="twitter:title" content="Title for Twitter">',
            '<meta name="twitter:image" content="https://example.com/image.jpg">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testCanonicalTag(): void
    {
        $this->seoManager->method('getCanonical')->willReturn('https://example.com/page');

        $result = $this->builder->buildCanonical();

        $this->assertSame('<link rel="canonical" href="https://example.com/page">', $result);
    }

    public function testVerificationTags(): void
    {
        $this->seoManager->method('getVerifications')->willReturn([
            'google-site-verification' => 'verification_code',
            'bing-site-verification' => 'another_verification_code',
        ]);

        $result = $this->builder->buildVerificationTags();

        $expected = [
            '<meta name="google-site-verification" content="verification_code">',
            '<meta name="bing-site-verification" content="another_verification_code">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testHreflangs(): void
    {
        $this->seoManager->method('getHreflangs')->willReturn([
            'en' => 'https://example.com/en',
            'fr' => 'https://example.com/fr',
        ]);

        $result = $this->builder->buildHreflangs();

        $expected = [
            '<link rel="alternate" hreflang="en" href="https://example.com/en">',
            '<link rel="alternate" hreflang="fr" href="https://example.com/fr">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }

    public function testBuildAllTags(): void
    {
        $this->seoManager->method('getFullTitle')->willReturn('My Title');
        $this->seoManager->method('getDescription')->willReturn('My Description');
        $this->seoManager->method('getCanonical')->willReturn('https://example.com/page');

        $this->seoManager->method('getMetaCollection')->willReturn(
            new MetaTagCollection([
                MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                MetaTag::ROBOTS->value => 'index, follow',
                MetaTag::VIEWPORT->value => '',
            ])
        );
        $this->seoManager->method('getMeta')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        MetaTag::KEYWORDS->value => 'keyword1, keyword2',
                        MetaTag::ROBOTS->value => 'index, follow',
                        MetaTag::VIEWPORT->value => '',
                        default => null,
                    };
                }
            );

        $this->seoManager->method('getOpenGraphCollection')->willReturn(
            new OpenGraphTagCollection([
                OpenGraphTag::TITLE->value => 'keyword1, keyword2',
                OpenGraphTag::TYPE->value => 'index, follow',
            ])
        );
        $this->seoManager->method('getOpenGraph')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        OpenGraphTag::TITLE->value => 'open graph title',
                        OpenGraphTag::TYPE->value => 'website',
                        default => null,
                    };
                }
            );

        $this->seoManager->method('getTwitterCollection')->willReturn(
            new TwitterTagCollection([
                TwitterCardTag::TITLE->value => 'twitter title',
                TwitterCardTag::CARD->value => TwitterCardType::PHOTO,
            ])
        );
        $this->seoManager->method('getTwitter')
            ->willReturnCallback(
                function (string $tag) {
                    return match ($tag) {
                        TwitterCardTag::TITLE->value => 'twitter title',
                        TwitterCardTag::CARD->value => 'photo',
                        default => null,
                    };
                }
            );

        $this->seoManager->method('getVerifications')->willReturn([
            'google-site-verification' => 'verification_code',
        ]);
        $this->seoManager->method('getHreflangs')->willReturn([
            'en' => 'https://example.com/en',
        ]);

        $this->seoManager->method('isNoIndex')->willReturn(true);
        $this->seoManager->method('isAdultContent')->willReturn(true);

        $result = $this->builder->buildAllTags();

        $expected = [
            '<title>My Title</title>',
            '<meta name="description" content="My Description">',
            '<meta name="robots" content="noindex, nofollow">',
            '<meta name="keywords" content="keyword1, keyword2">',
            '<meta name="rating" content="adult">',
            '<meta property="og:title" content="open graph title">',
            '<meta property="og:type" content="website">',
            '<meta name="twitter:title" content="twitter title">',
            '<meta name="twitter:card" content="photo">',
            '<link rel="canonical" href="https://example.com/page">',
            '<meta name="google-site-verification" content="verification_code">',
            '<link rel="alternate" hreflang="en" href="https://example.com/en">',
        ];

        $this->assertSame(implode("\n", $expected), $result);
    }
}
