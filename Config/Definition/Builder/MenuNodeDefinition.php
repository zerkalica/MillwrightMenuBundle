<?php

namespace Millwright\MenuBundle\Config\Definition\Builder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
                ->arrayNode('roles')
                    ->defaultValue(array())
                    ->beforeNormalization()->ifString()->then(function($v) { return preg_split('/\s*,\s*/', $v); })->end()
                    ->prototype('scalar')->end()
                    ->end()
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
