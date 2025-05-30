<?php

namespace Gurtok\SeoBundle\Tests\Fixtures;

use Gurtok\SeoBundle\Attribute\SeoMeta;
use Gurtok\SeoBundle\Service\SeoManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/regular-page', name: 'regular_page')]
    #[SeoMeta(
        titlePrefix: 'Regular Page',
        description: ['en' => 'Test Description English', 'fr' => 'Test Description French'],
        meta: [
            'theme-color' => 'dark',
        ],
        og: [
            'title' => 'Test Open Graph Title',
            'description' => 'Test Open Graph Description',
            'type' => 'website',
            'image' => 'https://example.com/og-test-image.jpg',
        ],
        twitter: [
            'card' => 'summary_large_image',
            'title' => 'Test Twitter Title',
            'description' => 'Test Twitter Description',
            'image' => 'https://example.com/test-twitter-image.jpg',
        ],
        verifications: [
            'google-site-verification' => 'test-google-verification',
        ],
        hreflangs: [
            'en' => 'https://example.com/en/regular-page',
            'fr' => 'https://example.com/fr/regular-page',
        ],
    )]
    public function regular(Request $request): Response
    {
        return new Response(
            content: '<html><head><title>Regular Page</title></head><body>Test</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/custom-page', name: 'custom_page')]
    #[SeoMeta(
        title: 'Custom Page Title',
        description: ['en' => 'Custom Page Description English', 'fr' => 'Custom Page Description French'],
        autoGenerateCanonical: false,
        meta: [
            'custom-tag' => 'custom-information',
            'custom-tag-trans' => 'translated.custom.trans',
        ],
    )]
    public function custom(Request $request): Response
    {
        return new Response(
            content: '<html><head><title>Custom Page</title></head><body>Custom Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/no-meta-page', name: 'no_meta_page')]
    #[SeoMeta(
        autoGenerateCanonical: false,
        disableDefaults: true,
    )]
    public function noMeta(Request $request): Response
    {
        return new Response(
            content: '<html><head></head><body>No Meta Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/no-index-page', name: 'no_index_page')]
    #[SeoMeta(
        titleSeparator: ' | ',
        titlePrefix: 'No Index Page',
        description: 'This page should not be indexed by search engines.',
        noIndex: true,
    )]
    public function noIndex(Request $request): Response
    {
        return new Response(
            content: '<html><head></head><body>No Index Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/adult-content-page', name: 'adult_content_page')]
    #[SeoMeta(
        title: 'Adult Content Page',
        description: 'This page contains adult content.',
        isAdultContent: true,
        disableDefaults: true,
    )]
    public function adultContent(Request $request): Response
    {
        return new Response(
            content: '<html><head></head><body>Adult Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/disable-auto-inject', name: 'disable_auto_inject')]
    #[SeoMeta(
        title: 'Disabled Auto Inject Attribute Title',
        description: 'This page has auto-inject disabled.',
    )]
    public function disableAutoInject(Request $request): Response
    {
        return new Response(
            content: '<html><title>Disabled Auto Inject</title><head></head><body>Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/excluded-page', name: 'excluded_page')]
    #[SeoMeta(
        title: 'Excluded Route',
        description: 'This route is excluded from SEO processing.',
    )]
    public function excludedRoute(Request $request): Response
    {
        // This route is intentionally left empty to test exclusion from SEO processing.
        return new Response(
            content: '<html><head></head><body>Excluded Route Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/without-attribute', name: 'without_attribute')]
    public function withoutAttribute(Request $request): Response
    {
        // This method is intentionally left without an SEO attribute to test default behavior.
        return new Response(
            content: '<html><head></head><body>Without Attribute Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/not-html-response', name: 'not_html_response')]
    #[SeoMeta(
        title: 'Non-HTML Response',
        description: 'This response is not HTML and should ignore.',
    )]
    public function notHtmlResponse(Request $request): Response
    {
        // This method returns a non-HTML response to test handling of different content types.
        return new Response(
            content: '{"html": "<html><title>Json Response</title><head></head><body>Content</body></html>"}',
            headers: ['Content-Type' => 'application/json']
        );
    }

    #[Route('/update-seo', name: 'seo_update')]
    #[SeoMeta(
        title: 'News',
        description: 'This page will have its SEO attributes updated dynamically.',
    )]
    public function updateSeo(SeoManager $seoManager, Request $request): Response
    {
        $seoManager->setTitlePrefix('Post from database');

        // This method is intentionally left empty to test dynamic SEO updates.
        return new Response(
            content: '<html><head></head><body>Dynamically Updated Content</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/trans-page', name: 'trans_page')]
    #[SeoMeta(
        titlePrefix: 'translated.page_title',
    )]
    public function transPage(Request $request): Response
    {
        return new Response(
            content: '<html><head></head><body>Translation Test</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }

    #[Route('/spec-trans-page', name: 'spec_trans_page')]
    #[SeoMeta(
        titlePrefix: 'translated.page_title',
        translationDomain: 'specific_seo',
    )]
    public function specificTransDomainPage(Request $request): Response
    {
        // This method is intentionally left empty to test specific translation domain handling.
        return new Response(
            content: '<html><head></head><body>Specific Translation Domain Test</body></html>',
            headers: ['Content-Type' => 'text/html']
        );
    }
}
