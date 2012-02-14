<?php
/**
 * Translate domain interface added to Knp ItemInterface
 *
 * @author   Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 */
namespace Millwright\MenuBundle\Menu;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 */
interface MenuBuilderInterface
{
    /**
     * Create menu
     *
     * @param  string  $name name of menu container
     * @param  Request $request
     * @param  array   $defaultRouteParams default route params for options
     * @param  array   $routeParams
     * @see MenuFactoryInterface::setRouteParams()
     * @see MenuFactoryInterface::setDefaultRouteParams()
     * @return MenuItemInterface
     */
    public function createMenu($name,
        array $defaultRouteParams = array(),
        array $routeParams = array()
    );

    /**
     * Create single item without children (for menu link)
     *
     * @param  string  $name name of menu item
     * @param  array   $defaultRouteParams default route params for options
     * @param  array   $routeParams
     * @see MenuFactoryInterface::setRouteParams()
     * @see MenuFactoryInterface::setDefaultRouteParams()
     * @return MenuItemInterface
     */
    public function createLink($name, array $routeParams = array());


    /**
     * Load cache
     *
     * @param string $cacheDir
     */
    public function loadCache($cacheDir);
}
