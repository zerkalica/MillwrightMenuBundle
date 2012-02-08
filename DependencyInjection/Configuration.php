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

        $rootNode
            ->children()
                ->menuNode('route')->menuNodePlain()->end()
                ->arrayNode('menu')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('id')->end()
                        ->scalarNode('translateDomain')->end()
                        ->scalarNode('roles')->end()
                        ->menuNode('children')->menuNodeHierarhy()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
