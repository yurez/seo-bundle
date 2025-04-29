<?php

namespace Gurtok\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('gurtok_seo');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('allow_custom_meta')
                    ->defaultFalse()
                ->end()
                ->booleanNode('auto_inject_response')
                    ->defaultTrue()
                ->end()
                ->arrayNode('excluded_paths')
                    ->prototype('scalar')->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
