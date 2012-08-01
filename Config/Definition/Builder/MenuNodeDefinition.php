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
                ->arrayNode('translateParameters')
                    ->useAttributeAsKey('translateParameters')->prototype('scalar')->end()
                ->end()
                ->arrayNode('secureParams')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('class')->end()
                            ->arrayNode('permissions')
                                ->beforeNormalization()->ifString()->then(
                                    function($v) {
                                        return preg_split('/\s*,\s*/', $v);
                                    })->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('attributes')
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('id')->end()
                    ->end()
                ->end()
                ->arrayNode('linkAttributes')
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('id')->end()
                        ->scalarNode('target')->end()
                        ->scalarNode('title')->end()
                        ->scalarNode('rel')->end()
                        ->scalarNode('type')->end()
                        ->scalarNode('name')->end()
                        ->scalarNode('type')->end()
                    ->end()
                ->end()
                ->arrayNode('childrenAttributes')
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('id')->end()
                    ->end()
                ->end()
                ->arrayNode('labelAttributes')
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('id')->end()
                    ->end()
                ->end()
                ->scalarNode('display')->end()
                ->scalarNode('displayChildren')->end()
                ->scalarNode('route')->end()
                ->scalarNode('type')->end()
                ->booleanNode('routeAbsolute')->end()
                ->arrayNode('routeParameters')
                    ->useAttributeAsKey('routeParameters')->prototype('scalar')->end()
                ->end()
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
