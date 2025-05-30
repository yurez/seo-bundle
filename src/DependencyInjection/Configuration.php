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
                    ->info('Allow custom meta tags to be added in templates.')
                ->end()
                ->booleanNode('auto_inject_response')
                    ->defaultTrue()
                    ->info('Automatically inject SEO tags into HTML responses only. May override title or cause duplicates if used improperly.')
                ->end()
                ->arrayNode('excluded_paths')
                    ->scalarPrototype()->end()
                    ->defaultValue([])
                    ->info('List of paths where SEO tags will not be injected.')
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
                    ->info('List of query parameters that will be excluded from canonical URLs.')
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys(false)
                    ->info('Default SEO settings for the application.')
                    ->children()
                        ->variableNode('title')
                            ->info('Default title for the application. Can be a plain string, a translation key (slug), or an array of localized values.')
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
                            ->info('Separator used in the title. Default is " - ".')
                            ->defaultValue(' - ')
                            ->validate()
                                ->ifTrue(static fn ($v) => !\is_string($v))
                                ->thenInvalid('The "title_separator" must be a string.')
                            ->end()
                        ->end()
                        ->variableNode('description')
                            ->info('Default description for the application. Can be a plain string, a translation key (slug), or an array of localized values.')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(static fn ($v) => ['default' => $v])
                            ->end()
                            ->validate()
                                ->ifTrue(static fn ($v) => !\is_array($v))
                                ->thenInvalid('The "description" must be a string or a localized array.')
                            ->end()
                        ->end()
                        ->scalarNode('auto_canonical')
                            ->info('If set to true (by default), the canonical URL will be generated automatically based on the request.')
                            ->defaultTrue()
                            ->end()
                        ->arrayNode('meta')
                            ->info('Default meta tags for the application.')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('og')
                            ->info('Default Open Graph tags for the application.')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('twitter')
                            ->info('Default Twitter Card tags for the application.')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('verifications')
                            ->info('Verification tags for the application. These are used for services like Google Search Console, Bing Webmaster Tools, etc.')
                            ->normalizeKeys(false)
                            ->scalarPrototype()->end()
                        ->end()
                        ->booleanNode('no_index')
                            ->info('If set to true, the application will be marked as no-index, meaning search engines should not index it.')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('is_adult_content')
                            ->info('If set to true, the application will be marked as adult content, which may affect how search engines treat it.')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('translation_domain')
                            ->defaultNull()
                            ->info('Translation domain used for resolving translatable SEO values. If null, the default domain is used.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
