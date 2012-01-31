<?php

namespace Millwright\MenuBundle\Config\Definition\Builder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Configuration definition for menu nodes
 */
class MenuNodeDefinition extends ArrayNodeDefinition
{
    public function menuNodeHierarhy($depth = 10)
    {
        if ($depth == 0) {
            return $this;
        }

        return $this
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->children()
                ->scalarNode('id')->end()
                ->scalarNode('name')->end()
                ->scalarNode('uri')->end()
                ->arrayNode('role')
                    ->defaultValue(array())
                    ->beforeNormalization()->ifString()->then(
                        function($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })->end()
                    ->prototype('scalar')->end()
                    ->end()
                ->scalarNode('label')->end()
                ->scalarNode('translateDomain')->end()
                ->scalarNode('translateParameters')->defaultValue(array())->end()
                ->scalarNode('attributes')->defaultValue(array())->end()
                ->scalarNode('linkAttributes')->defaultValue(array())->end()
                ->scalarNode('childrenAttributes')->defaultValue(array())->end()
                ->scalarNode('labelAttributes')->defaultValue(array())->end()
                ->scalarNode('display')->defaultValue(true)->end()
                ->scalarNode('displayChildren')->defaultValue(true)->end()
                ->scalarNode('route')->end()
                ->arrayNode('routeParameters')->defaultValue(array())->end()
                ->booleanNode('routeAbsolute')->defaultValue(false)->end()
                ->menuNode('children')->menuNodeHierarhy($depth - 1)->end()
            ->end();
    }
}
