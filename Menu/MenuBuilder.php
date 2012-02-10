<?php
/**
 * Menu builder
 *
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
namespace Millwright\MenuBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\ConfigCache;
use Millwright\MenuBundle\Config\OptionMergerInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuBuilder implements MenuBuilderInterface
{
    /**
     * @var MenuFactoryInterface
     */
    private $factory;

    /**
     * @var OptionMergerInterface
     */
    private $merger;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $menuOptions;

    public function __construct(
        MenuFactoryInterface     $factory,
        OptionMergerInterface    $merger,
        array                    $options
    ) {
        $this->factory     = $factory;
        $this->merger      = $merger;
        $this->options     = $options;
    }

    /**
     * Get static part of menu item options
     *
     * @param  string $name menu container name
     * @return array
     */
    private function getMenuOptions($name)
    {
        if(null === $this->menuOptions) {
            $class = $this->options['generator_cache_class'];
            $cache = $this->options['cache_dir']
                ? new ConfigCache($this->options['cache_dir'] . '/'
                    . $class . '.php',
                    $this->options['debug'])
                : null
            ;

            if(!$cache || !$cache->isFresh()) {

                $this->menuOptions = $this->merger->normalize(
                    $this->options['tree'],
                    $this->options['items']);

                $cache->write('return ' . var_export($this->menuOptions, true) . ';');
            } else {
                $this->menuOptions = require_once $cache;
            }
        }

        return $this->menuOptions[$name];
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::create()
     */
    public function create($name,
        array $defaultRouteParams = array(),
        array $routeParams = array()
    )
    {
        $options = $this->getMenuOptions($name);
        $factory = clone $this->factory;

        $factory
            ->setDefaultRouteParams($defaultRouteParams)
            ->setRouteParams($routeParams)
        ;

        return $factory->createFromArray($options);
    }
}
