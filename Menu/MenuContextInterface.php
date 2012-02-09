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
interface MenuContextInterface
{
    /**
     * Set variable context: route parameters and load security visibility
     *
     * @param  MenuItemInterface $item
     * @param  array $routeParameters
     * @param  boolean|false $recursive if true - set route parameters to child items
     * @return MenuContextInterface
     */
    public function setContext(MenuItemInterface $item,
        array $routeParameters = array(),
        $recursive = false);
}
