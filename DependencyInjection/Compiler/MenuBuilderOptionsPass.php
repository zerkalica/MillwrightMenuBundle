<?php
namespace Millwright\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Definition\Processor;

use Millwright\Util\DependencyInjection\ContainerUtil;

use Millwright\MenuBundle\DependencyInjection\MenuConfiguration;

class MenuBuilderOptionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $normalizer = function(array &$config, Processor $processor, ContainerBuilder $container)
        {
            //reuse configuration for validating service-provided menu configs
            $config = $processor->processConfiguration(new MenuConfiguration, array('millwright_menu' => $config));

            if(isset($config['renderers'])) {
                $renderers = $config['renderers'];
                foreach($config['tree'] as & $tree) {
                    if(isset($tree['type'])) {
                        $type = $tree['type'];
                        if(isset($renderers[$type])) {
                            $renderOption = $renderers[$type];
                            foreach(array('attributes') as $option) {
                                if(isset($renderOption[$option])) {
                                    $tree[$option] = $renderOption[$option];
                                }
                            }
                        }
                    }
                }
            }
        };

        $config = ContainerUtil::collectConfiguration(
            'millwright_menu.menu_options',
            $container,
            $normalizer
        );

        $container->getDefinition('millwright_menu.helper')
            ->replaceArgument(2, $config['renderers']);

        unset($config['renderers']);

        $container->getDefinition('millwright_menu.option.builder')
            ->addMethodCall('setDefaults', array($config));
    }
}
