<?php

declare(strict_types=1);

use Gurtok\SeoBundle\EventListener\SeoAttributeListener;
use Gurtok\SeoBundle\EventListener\SeoResponseListener;
use Gurtok\SeoBundle\Service\SeoManager;
use Gurtok\SeoBundle\Twig\SeoExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(SeoManager::class);

    $services->set(SeoExtension::class)
        ->tag('twig.extension');

    $services->set(SeoAttributeListener::class)
        ->args([
            service(SeoManager::class),
            service('request_stack'),
            service('translator')->nullOnInvalid(),
            '%kernel.default_locale%',
            '%gurtok_seo.allow_custom_meta%',
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.controller']);

    $services->set(SeoResponseListener::class)
        ->args([
            service(SeoManager::class),
            service('twig'),
            '%gurtok_seo.auto_inject_response%',
            '%gurtok_seo.excluded_paths%',
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.response']);
};
