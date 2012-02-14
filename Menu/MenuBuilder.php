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

    /**
     * @var array
     */
    private $compiledOptions;


    public function __construct(
        MenuFactoryInterface  $factory,
        OptionMergerInterface $merger,
        array                 $options,
        array                 $menuOptions
    ) {
        $this->factory     = $factory;
        $this->merger      = $merger;
        $this->options     = $options;
        $this->menuOptions = $menuOptions;
    }

    public function loadCache($cacheDir = null)
    {
        if(null === $this->compiledOptions) {
            if(!$cacheDir) {
                $cacheDir = $this->options['cache_dir'];
            }

            $class = $this->options['generator_cache_class'];
            $cache = $cacheDir
                ? new ConfigCache($cacheDir . '/'
                    . $class . '.php',
                    $this->options['debug'])
                : null
            ;

            if(!$cache || !$cache->isFresh()) {
                $this->compiledOptions = $this->merger->normalize($this->menuOptions);
                $cache->write('return ' . var_export($this->compiledOptions, true) . ';');
            } else {
                $this->compiledOptions = require_once $cache;
            }
        }
    }

    /**
     * Get static part of menu item options
     *
     * @param  string $name menu container name
     * @return array
     */
    private function getMenuOptions($name)
    {
        $this->loadCache();

        return $this->compiledOptions['tree'][$name];
    }

    /**
     * Get menu item options
     *
     * @param  string $name menu item name
     * @return array
     */
    private function getLinkOptions($name)
    {
        $this->loadCache();

        return $this->compiledOptions['items'][$name];
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createMenu()
     */
    public function createMenu($name,
        array $defaultRouteParams = array(),
        array $routeParams = array()
    )
    {
        $options = $this->getMenuOptions($name);

        //@todo How to pass route params ?
        //1. remove factory from service and create new instance here ?
        //2. clone factory and replace route params (faster?)
        //3. add parameters to options array
        //4. remove per item routeParams, use only per menu defaultRouteParams
        $factory = clone $this->factory;

        $factory
            ->setDefaultRouteParams($defaultRouteParams)
            ->setRouteParams($routeParams)
        ;

        return $factory->createFromArray($options);
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createLink()
     */
    public function createLink($name, array $defaultRouteParams = array())
    {
        $options = $this->getLinkOptions($name);
        $factory = clone $this->factory;

        $factory
            ->setDefaultRouteParams($defaultRouteParams)
            ->setRouteParams(array())
        ;

        return $factory->createItem($name, $options);
    }
}
