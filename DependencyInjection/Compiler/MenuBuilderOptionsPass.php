<?php
namespace Millwright\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Definition\Processor;
use Millwright\MenuBundle\DependencyInjection\Configuration;

class MenuBuilderOptionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('millwright_menu.builder')) {
            return;
        }

        $menuContainers = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds('millwright_menu.menu_options') as $id => $tags) {

            $priority = isset($attributes[0]['order']) ? $attributes[0]['order'] : 0;
            $definition = $container->getDefinition($id);

            $data = $definition->getArgument(0);
            $menuContainers->insert($data, $priority);
        }

        $menuContainers = iterator_to_array($menuContainers);
        ksort($menuContainers);

        $config = array();
        foreach ($menuContainers as $bundleConfig) {
            $config = $this->merge($config, $bundleConfig);
        }

        //$config = $container->get('millwright_menu.merger')->normalize($config);
        #$processor     = new Processor();
        #$configuration = new Configuration();
        #$config = $processor->processConfiguration($configuration, $config);

        $builderDefinition = $container->getDefinition('millwright_menu.builder');
        $builderDefinition->replaceArgument(3, $config);

        if(isset($config['renderers'])) {
            $container->getDefinition('millwright_menu.helper')
                ->replaceArgument(2, $config['renderers']);
        }
    }

    private function merge($to, $from)
    {
        foreach ($from as $key => $value) {
            if (!is_array($value)) {
                if (is_int($key)) {
                    $to[] = $value;
                } else {
                    $to[$key] = $value;
                }
            } else {

                if (!isset($to[$key])) {
                    $to[$key] = array();
                }

                $to[$key] = $this->merge($to[$key], $value);
            }
        }

        return $to;
    }
}
