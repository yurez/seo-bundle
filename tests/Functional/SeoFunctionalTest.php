<?php

namespace Gurtok\SeoBundle\Tests\Functional;

use Gurtok\SeoBundle\EventListener\SeoDefaultsListener;
use Gurtok\SeoBundle\EventListener\SeoResponseListener;
use Gurtok\SeoBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeoFunctionalTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    private function getResponseContent(string $uri, string $env = 'default'): string
    {
        $client = static::createClient(['environment' => $env]);
        $client->request('GET', $uri);
        self::assertResponseIsSuccessful();

        return (string) $client->getResponse()->getContent();
    }

    public function testListenerAddsMeta(): void
    {
        $content = $this->getResponseContent('/regular-page');
        $this->assertStringContainsString(
            '<title>Regular Page - Default title</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Test Description English">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, web, club">', // take it from default
            $content
        );
        $this->assertStringContainsString(
            '<meta name="theme-color" content="dark">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Test Open Graph Title">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Test Open Graph Description">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="https://example.com/og-test-image.jpg">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:type" content="website">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Test Twitter Title">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Test Twitter Description">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary_large_image">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:image" content="http://localhost/test-twitter-image.jpg">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/regular-page">', // automatically generated
            $content
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="test-google-verification">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="alternate" hreflang="en" href="https://example.com/en/regular-page">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="alternate" hreflang="fr" href="https://example.com/fr/regular-page">',
            $content
        );
    }

    public function testListenerAddsCustomMetaWithoutCanonicalTag(): void
    {
        $content = $this->getResponseContent('/custom-page', 'custom');

        $this->assertStringContainsString(
            '<title>Custom Page Title</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="custom-tag" content="custom-information">',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            $content
        ); // no canonical tag
    }

    public function testListenerWithNoMeta(): void
    {
        $content = $this->getResponseContent('/no-meta-page');

        $this->assertStringNotContainsString(
            '<title>',
            $content
        );
        $this->assertStringNotContainsString(
            '<meta',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="alternate"',
            $content
        );
    }

    public function testListenerWithNoIndex(): void
    {
        $content = $this->getResponseContent('/no-index-page');

        $this->assertStringContainsString(
            '<title>No Index Page | Default title</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="description" content="This page should not be indexed by search engines.">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="robots" content="noindex, nofollow">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, web, club">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Default og title">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Default og description">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="http://localhost/images/default.png">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Default twitter title like string">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Default twitter description">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:image" content="https://example.com/images/default.png">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary">', // from defaults
            $content
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/no-index-page">', // auto generated
            $content
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="google_verification_code_default">', // from defaults
            $content
        );
    }

    public function testListenerWithAdultContent(): void
    {
        $content = $this->getResponseContent('/adult-content-page');
        $this->assertStringContainsString(
            '<title>Adult Content Page</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="description" content="This page contains adult content.">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="rating" content="adult">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="rating" content="RTA-5042-1996-1400-1577-RTA">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/adult-content-page">',
            $content
        );
    }

    public function testListenerWithDisabledAutoInjectSeo(): void
    {
        $content = $this->getResponseContent('/disable-auto-inject', 'noseo');

        $this->assertStringContainsString(
            '<title>Disabled Auto Inject</title>',
            $content
        );
        $this->assertStringNotContainsString(
            '<meta',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="alternate"',
            $content
        );
    }

    public function testExcludedRoutes(): void
    {
        $content = $this->getResponseContent('/excluded-page');
        $this->assertStringNotContainsString(
            '<title>',
            $content
        );
        $this->assertStringNotContainsString(
            '<meta',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            $content
        );
    }

    public function testWithoutAttributeTakeFromDefaults(): void
    {
        $content = $this->getResponseContent('/without-attribute', 'default');
        $this->assertStringContainsString(
            '<title>Default title</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="description" content="Default description from Default">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="keywords" content="seo, web, club">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Default og title">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="Default og description">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="http://localhost/images/default.png">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="https://example.com/canonical">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Default twitter title like string">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="Default twitter description">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/without-attribute">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="google-site-verification" content="google_verification_code_default">',
            $content
        );
    }

    public function testNoneHtmlResponse(): void
    {
        $content = $this->getResponseContent('/not-html-response');
        $this->assertStringNotContainsString(
            '<meta',
            $content
        );
        $this->assertStringNotContainsString(
            '<link rel="canonical"',
            $content
        );
    }

    public function testDisableListeners(): void
    {
        $client = static::createClient(['environment' => 'disable_listeners']);

        $client->request('GET', '/regular-page');

        $container = static::getContainer();

        $this->assertFalse(
            $container->has(SeoDefaultsListener::class),
            'SeoDefaultsListener should not be loaded in this environment'
        );

        $this->assertFalse(
            $container->has(SeoResponseListener::class),
            'SeoResponseListener should not be loaded in this environment'
        );
    }

    public function testSeoUpdate(): void
    {
        $content = $this->getResponseContent('/update-seo');

        $this->assertStringContainsString(
            '<title>Post from database - News</title>',
            $content
        );
    }

    public function testAutoGenerateSeoAttributes()
    {
        $content = $this->getResponseContent('/simple-page', 'simple');

        $this->assertStringContainsString(
            '<title>Simple Page Title</title>',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="description" content="This is a simple page with default SEO attributes.">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="robots" content="index, follow">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:image" content="http://localhost/images/simple-page-og-image.jpg">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:type" content="article">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:title" content="Simple Page Title">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="This is a simple page with default SEO attributes.">',
            $content
        );
        $this->assertStringContainsString(
            '<meta property="og:url" content="http://localhost/simple-page">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:card" content="summary">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:image" content="http://example.com/images/simple-page-twitter-image.jpg">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:title" content="Simple Page Title">',
            $content
        );
        $this->assertStringContainsString(
            '<meta name="twitter:description" content="This is a simple page with default SEO attributes.">',
            $content
        );
        $this->assertStringContainsString(
            '<link rel="canonical" href="http://localhost/simple-page">',
            $content
        );
    }
}
