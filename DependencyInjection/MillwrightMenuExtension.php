<?php
namespace Millwright\MenuBundle\DependencyInjection;

use Millwright\Util\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MillwrightMenuExtension extends Extension
{
    protected $bundleRoot = __DIR__;
    protected $isYml      = false;

    /**
     * {@inheritDoc}
     */
    protected function getConfigParts()
    {
        return array(
            'services.xml',
            'twig.xml',
        );
    }
}
