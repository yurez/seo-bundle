<?php

namespace Gurtok\SeoBundle\Tests\Functional;

use Gurtok\SeoBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeoTranslationTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testWithoutAttributeTakeFromDefaults(): void
    {
        $client = static::createClient(['environment' => 'default']);

        $client->request(
            'GET',
            '/without-attribute',
            server: [
                'HTTP_ACCEPT_LANGUAGE' => 'uk',
            ]
        );

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Заголовок українською</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Опис українською">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, веб, гурток">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="og заголовок українською">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="og опис українською">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Default twitter title like string">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="twitter опис українською">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/without-attribute">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="google_verification_code_default">',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testListenerAddsCustomMetaWithTranslationsWithDefaultDomain(): void
    {
        $client = static::createClient(['environment' => 'custom']);

        $client->request(
            'GET',
            '/custom-page',
            server: [
                'HTTP_ACCEPT_LANGUAGE' => 'fr',
            ]
        );

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Custom Page Title</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Custom Page Description French">',
            (string) $client->getResponse()->getContent()
        );
        $crawler = $client->getCrawler();
        $this->assertSame(
            'Traduction personnalisée',
            $crawler->filter('meta[name="custom-tag-trans"]')->attr('content')
        );
    }

    public function testTranslationWithDefaultLocale(): void
    {
        $client = static::createClient(['environment' => 'translation']);

        $client->request('GET', '/trans-page');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>SEO Page Title - SEO Title</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="SEO Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="SEO, Web, Club">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="SEO OG Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="SEO OG Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="SEO Twitter Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="SEO Twitter Description">',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testTranslationWithSetUpLocale(): void
    {
        $client = static::createClient(['environment' => 'translation']);

        $client->request(
            'GET',
            '/trans-page',
            server: [
                'HTTP_ACCEPT_LANGUAGE' => 'uk',
            ]
        );

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>SEO Заголовок Сторінки - SEO Заголовок</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="SEO Опис">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="SEO, Веб, Клуб">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="SEO OG Заголовок">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="SEO OG Опис">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="SEO Twitter Заголовок">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="SEO Twitter Опис">',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testTranslationWithSpecificDomain(): void
    {
        $client = static::createClient(['environment' => 'translation']);

        $client->request('GET', '/spec-trans-page');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Specific SEO Page Title - Specific SEO Title</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Specific SEO Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="Specific SEO, Web, Club">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Specific SEO OG Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Specific SEO OG Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Specific SEO Twitter Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Specific SEO Twitter Description">',
            (string) $client->getResponse()->getContent()
        );
    }
}
