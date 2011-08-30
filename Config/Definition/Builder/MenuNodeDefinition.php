<?php

namespace Millwright\MenuBundle\Config\Definition\Builder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Configuration definition for menu nodes
 */
class MenuNodeDefinition extends ArrayNodeDefinition
{
    public function menuNodeHierarhy($depth = 3)
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
                ->scalarNode('role')->end()
                ->scalarNode('label')->end()
                ->scalarNode('translateParams')->defaultValue(array())->end()
                ->scalarNode('domain')->end()
                ->scalarNode('route')->end()
                ->arrayNode('routeParams')->end()
                ->booleanNode('absolute')->end()
                ->menuNode('submenu')->menuNodeHierarhy($depth - 1)->end()
            ->end();
    }
}
