<?php
namespace Millwright\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

use Millwright\ConfigurationBundle\DependencyInjection\Compiler\OptionCompilerPassBase;

use Millwright\MenuBundle\DependencyInjection\MenuConfiguration;

class MenuBuilderOptionsPass extends OptionCompilerPassBase
{
    protected $optionBuilderId = 'millwright_menu.option.builder';
    protected $optionsTag      = 'millwright_menu.menu_options';

    protected function preProcess(array & $config, Processor $processor, ContainerBuilder $container)
    {
        //reuse configuration for validating service-provided menu configs
        $config = array('millwright_menu' => $config);
        $config = $processor->processConfiguration(new MenuConfiguration, $config);

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
    }
}
