<?php

namespace Gurtok\SeoBundle\Tests\Functional;

use Gurtok\SeoBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeoListenerFunctionalTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testListenerAddsMeta(): void
    {
        $client = static::createClient();

        $client->request('GET', '/regular-page');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Regular Page - Default title</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Test Description English">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, web, club">', // take it from default
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="charset" content="UTF-8">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Test Open Graph Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Test Open Graph Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="https://example.com/og-test-image.jpg">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:type" content="website">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Test Twitter Title">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Test Twitter Description">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary_large_image">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:image" content="https://example.com/test-twitter-image.jpg">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/regular-page">', // automatically generated
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="test-google-verification">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="alternate" hreflang="en" href="https://example.com/en/regular-page">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="alternate" hreflang="fr" href="https://example.com/fr/regular-page">',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testListenerAddsCustomMeta(): void
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
        $this->assertStringContainsString(
            '<meta name="custom-tag" content="custom-information">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            (string) $client->getResponse()->getContent()
        ); // no canonical tag
    }

    public function testListenerWithNoMeta(): void
    {
        $client = static::createClient();

        $client->request('GET', '/no-meta-page');

        self::assertResponseIsSuccessful();
        $this->assertStringNotContainsString(
            '<title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<meta',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="alternate"',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testListenerWithNoIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/no-index-page');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>No Index Page | Default title</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="This page should not be indexed by search engines.">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="robots" content="noindex, nofollow">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, web, club">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="charset" content="UTF-8">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Default og title">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Default og description">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="/images/default.png">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Default twitter title like string">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Default twitter description">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary">', // from defaults
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/no-index-page">', // auto generated
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="google_verification_code_default">', // from defaults
            (string) $client->getResponse()->getContent()
        );
    }

    public function testListenerWithAdultContent(): void
    {
        $client = static::createClient();

        $client->request('GET', '/adult-content-page');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Adult Content Page</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="description" content="This page contains adult content.">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="rating" content="adult">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="rating" content="RTA-5042-1996-1400-1577-RTA">',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/adult-content-page">',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testListenerWithDisabledAutoInjectSeo(): void
    {
        $client = static::createClient(['environment' => 'noseo']);

        $client->request('GET', '/disable-auto-inject');

        self::assertResponseIsSuccessful();
        $this->assertStringContainsString(
            '<title>Disabled Auto Inject</title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<meta',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="alternate"',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testExcludedRoutes(): void
    {
        $client = static::createClient();

        $client->request('GET', '/excluded-page');

        self::assertResponseIsSuccessful();
        $this->assertStringNotContainsString(
            '<title>',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<meta',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            (string) $client->getResponse()->getContent()
        );
    }

    public function testWithoutAttribute(): void
    {
        $client = static::createClient();

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
            '<meta name="charset" content="UTF-8">',
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
            '<meta property="og:image" content="/images/default.png">',
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

    public function testNoneHtmlResponse(): void
    {
        $client = static::createClient();

        $client->request('GET', '/not-html-response');

        self::assertResponseIsSuccessful();
        $this->assertStringNotContainsString(
            '<meta',
            (string) $client->getResponse()->getContent()
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            (string) $client->getResponse()->getContent()
        );
    }
}
