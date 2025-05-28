<?php

namespace Gurtok\SeoBundle\DependencyInjection;

use Gurtok\SeoBundle\EventListener\SeoDefaultsListener;
use Gurtok\SeoBundle\EventListener\SeoResponseListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class GurtokSeoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var array<string, string|int|bool|float|array<string, string>|\UnitEnum|null> $config */
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter('gurtok_seo.'.$key, $value);
        }

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $this->disableListeners($container);
    }

    protected function disableListeners(ContainerBuilder $container): void
    {
        $defaults = $container->getParameter('gurtok_seo.defaults');

        $isEmptyDefaults = $defaults
            && ($defaults['title'] ?? null) === null
            && ($defaults['description'] ?? null) === null
            && empty($defaults['meta'] ?? [])
            && empty($defaults['og'] ?? [])
            && empty($defaults['twitter'] ?? [])
            && empty($defaults['verifications'] ?? [])
            && ($defaults['is_adult_content'] ?? false) === false
            && ($defaults['no_index'] ?? false) === false;

        if ($isEmptyDefaults) {
            $container->removeDefinition(SeoDefaultsListener::class);
        }

        $autoInjectResponse = $container->getParameter('gurtok_seo.auto_inject_response');

        if (!$autoInjectResponse) {
            $container->removeDefinition(SeoResponseListener::class);
        }
    }
}
