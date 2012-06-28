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
class MenuConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('millwright_menu', 'array', new MenuTreeBuilder());

        $node = $rootNode
            ->children();

        $this->setChildren($node);
        $node->end();

        return $treeBuilder;
    }

    protected function setChildren($node)
    {
        $node->
        arrayNode('renderers')
            ->useAttributeAsKey('type')
            ->prototype('array')
                ->children()
                    ->arrayNode('attributes')
                        ->children()
                            ->scalarNode('class')->end()
                            ->scalarNode('id')->end()
                        ->end()
                    ->end()
                    ->scalarNode('renderer')->defaultValue(null)->end()
                    ->arrayNode('rendererOptions')
                        ->children()
                            ->scalarNode('template')->end()
                            ->scalarNode('clear_matcher')->defaultValue(false)->end()
                            ->scalarNode('depth')->end()
                            ->scalarNode('currentAsLink')->end()
                            ->scalarNode('currentClass')->end()
                            ->scalarNode('ancestorClass')->end()
                            ->scalarNode('firstClass')->end()
                            ->scalarNode('lastClass')->end()
                            ->scalarNode('compressed')->end()
                            ->scalarNode('block')->end()
                            ->scalarNode('rootClass')->end()
                            ->scalarNode('isDropdown')->defaultValue(true)->end()
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
        ->end();

        return $this;
    }
}
