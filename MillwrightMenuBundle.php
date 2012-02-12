<?php

namespace Millwright\MenuBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Millwright\MenuBundle\DependencyInjection\Compiler\MenuBuilderOptionsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MillwrightMenuBundle extends Bundle
{

    /**
     * {@inheritdoc}
     * @see Symfony\Component\HttpKernel\Bundle.Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuBuilderOptionsPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
