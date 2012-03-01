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

        return $this->beginTree()
                ->menuNode('children')->menuNodeHierarhy($depth - 1)
                    ->end() //children
                ->end(); // prototype
    }

    protected function beginTree()
    {
        return $this
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->children()
                ->scalarNode('id')->end()
                ->scalarNode('name')->end()
                ->scalarNode('uri')->end()
                ->arrayNode('roles')
                    ->beforeNormalization()->ifString()->then(
                        function($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })->end()
                    ->prototype('scalar')->end()
                    ->end()
                ->scalarNode('label')->end()
                ->scalarNode('translateDomain')->end()
                ->arrayNode('translateParameters')->end()
                ->arrayNode('attributes')
                    ->children()
                        ->scalarNode('class')->end()
                    ->end()
                ->end()
                ->arrayNode('linkAttributes')->end()
                ->arrayNode('childrenAttributes')->end()
                ->arrayNode('labelAttributes')->end()
                ->scalarNode('display')->end()
                ->scalarNode('displayChildren')->end()
                ->scalarNode('route')->end()
                ->scalarNode('type')->end()
                ->booleanNode('routeAbsolute')->end()
                ->booleanNode('showNonAuthorized')->end()
                ->booleanNode('showAsText')->end();
    }

    public function menuNodePlain()
    {
        return
            $this->beginTree()
                ->end() //children
            ->end(); //prototype
    }

}
