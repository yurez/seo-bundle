<?php

namespace Gurtok\SeoBundle\Tests\Fixtures;

use Gurtok\SeoBundle\GurtokSeoBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new GurtokSeoBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/packages/framework.yaml');
        $loader->load(__DIR__.'/config/packages/twig.yaml');
        $loader->load(__DIR__.'/config/packages/gurtok_seo.yaml');
        $loader->load(__DIR__.'/config/services_test.yaml');
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/config/routes.yaml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addResource(new FileResource(__FILE__));
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/gurtok_seo/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/gurtok_seo/log/'.$this->environment;
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }
}
