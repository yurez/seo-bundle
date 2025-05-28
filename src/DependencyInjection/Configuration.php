<?php

namespace Gurtok\SeoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('gurtok_seo');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->booleanNode('allow_custom_meta')
                    ->defaultFalse()
                ->end()
                ->booleanNode('auto_inject_response')
                    ->defaultTrue()
                ->end()
                ->arrayNode('excluded_paths')
                    ->scalarPrototype()->end()
                    ->defaultValue([])
                ->end()
                ->arrayNode('canonical_excluded_query_keys')
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        'utm_source',
                        'utm_medium',
                        'utm_campaign',
                        'utm_term',
                        'utm_content',
                        'ref',
                        'fbclid',
                        'page',
                    ])
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->variableNode('title')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static fn ($v) => ['default' => $v])
                            ->end()
                            ->validate()
                                ->ifTrue(static fn ($v) => !\is_array($v))
                                ->thenInvalid('The "title" must be a string or a localized array.')
                            ->end()
                        ->end()
                        ->scalarNode('title_separator')
                            ->defaultValue(' - ')
                            ->validate()
                                ->ifTrue(static fn ($v) => !\is_string($v))
                                ->thenInvalid('The "title_separator" must be a string.')
                            ->end()
                        ->end()
                        ->variableNode('description')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static fn ($v) => ['default' => $v])
                            ->end()
                            ->validate()
                                ->ifTrue(static fn ($v) => !\is_array($v))
                                ->thenInvalid('The "description" must be a string or a localized array.')
                            ->end()
                        ->end()
                        ->scalarNode('auto_canonical')->defaultTrue()->end()
                        ->arrayNode('meta')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('og')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('twitter')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('verifications')
                            ->normalizeKeys(false)
                            ->scalarPrototype()->end()
                        ->end()
                        ->booleanNode('no_index')->defaultFalse()->end()
                        ->booleanNode('is_adult_content')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
