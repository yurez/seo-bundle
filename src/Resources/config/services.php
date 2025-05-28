<?php

declare(strict_types=1);

use Gurtok\SeoBundle\EventListener\SeoAttributeListener;
use Gurtok\SeoBundle\EventListener\SeoDefaultsListener;
use Gurtok\SeoBundle\EventListener\SeoResponseListener;
use Gurtok\SeoBundle\Service\CanonicalUrlGenerator;
use Gurtok\SeoBundle\Service\LocalizedResolver;
use Gurtok\SeoBundle\Service\SeoDefaultsProvider;
use Gurtok\SeoBundle\Service\SeoManager;
use Gurtok\SeoBundle\Service\SeoTagHtmlBuilder;
use Gurtok\SeoBundle\Twig\SeoExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(SeoManager::class)
        ->args([
            service(LocalizedResolver::class),
            param('gurtok_seo.allow_custom_meta'),
        ]);

    $services->set(LocalizedResolver::class)
        ->args(
            [
                service('request_stack'),
                service('translator')->nullOnInvalid(),
                param('kernel.default_locale'),
            ]
        );

    $services->set(CanonicalUrlGenerator::class)
        ->args([
            service(UrlGeneratorInterface::class),
            param('gurtok_seo.canonical_excluded_query_keys'),
        ]);

    $services->set(SeoTagHtmlBuilder::class);

    $services->set(SeoExtension::class)
        ->args([
            service(SeoTagHtmlBuilder::class),
        ])
        ->tag('twig.extension');

    $services->set(SeoDefaultsProvider::class)
        ->args([
            param('gurtok_seo.defaults'),
            param('gurtok_seo.allow_custom_meta'),
        ]);

    $services->set(SeoDefaultsListener::class);

    $services->set(SeoAttributeListener::class)
        ->args([
            service(SeoManager::class),
            service(CanonicalUrlGenerator::class),
            param('gurtok_seo.allow_custom_meta'),
        ]);

    $services->set(SeoResponseListener::class)
        ->args([
            service(SeoManager::class),
            service('twig'),
            param('gurtok_seo.auto_inject_response'),
            param('gurtok_seo.excluded_paths'),
        ]);
};
