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
     * @param  array   $routeParams default route params to all routes
     * @return MenuItemInterface
     */
    public function create($name, array $routeParams = array());
}
