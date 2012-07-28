<?php
namespace Millwright\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Definition\Processor;

use Millwright\ConfigurationBundle\ContainerUtil as Util;

use Millwright\MenuBundle\DependencyInjection\MenuConfiguration;

class MenuBuilderOptionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('millwright_menu.builder')) {
            return;
        }

        $config = Util::collectConfiguration('millwright_menu.menu_options', $container);

        //@todo place normalization here and remove from menu builder and ConfigCache
        // why getRouteCollection falls here ?
        //$config = $container->get('millwright_menu.merger')->normalize($config);

        $processor     = new Processor();
        $configuration = new MenuConfiguration();

        //reuse configuration for validating service-provided menu configs
        $config = array('millwright_menu' => $config);
        $config = $processor->processConfiguration($configuration, $config);

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

            $container->getDefinition('millwright_menu.helper')
                ->replaceArgument(2, $config['renderers']);

            unset($config['renderers']);
        }

        $container->getDefinition('millwright_menu.option.builder')->addMethodCall('setDefaults', array($config));
    }
}
