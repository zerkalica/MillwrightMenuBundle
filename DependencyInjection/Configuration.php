<?php

namespace Millwright\MenuBundle\DependencyInjection;
use Millwright\MenuBundle\Config\Definition\Builder\MenuTreeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('millwright_menu', 'array', new MenuTreeBuilder());

        $node = $rootNode
            ->children()
                ->scalarNode('generator_cache_class')
                    ->defaultValue('%kernel.name%%kernel.environment%MenuTree')
                ->end()
                ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%')->end()
                ->scalarNode('debug')->defaultValue('%kernel.debug%')->end()
                ->arrayNode('renderers')
                    ->useAttributeAsKey('type')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('renderer')->defaultValue(null)->end()
                            ->arrayNode('rendererOptions')
                                ->children()
                                    ->scalarNode('template')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->menuNode('items')
                    ->menuNodePlain()
                ->end()
                ->menuNode('tree')
                    ->menuNodeHierarhy()
                ->end()

            ->end()
        ;

        return $treeBuilder;
    }
}
