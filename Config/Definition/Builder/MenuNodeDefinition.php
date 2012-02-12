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

    private function beginTree()
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
                ->scalarNode('translateParameters')->end()
                ->scalarNode('attributes')->end()
                ->scalarNode('linkAttributes')->end()
                ->scalarNode('childrenAttributes')->end()
                ->scalarNode('labelAttributes')->end()
                ->scalarNode('display')->end()
                ->scalarNode('displayChildren')->end()
                ->scalarNode('route')->end()
                ->arrayNode('rendererOptions')
                    ->children()
                        ->scalarNode('template')->end()
                    ->end()
                ->end()
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
