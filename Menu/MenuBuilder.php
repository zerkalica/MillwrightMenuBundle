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

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private $menuOptions;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, array $menuOptions = array())
    {
        $this->factory      = $factory;
        $this->menuOptions  = $menuOptions;
    }

    /**
     * Create menu
     *
     * @param  Request $request
     * @param  string  $name
     * @return ItemInterface
     */
    public function createMenu(Request $request, $name)
    {
        $options = $this->menuOptions[$name];
        $menu    = $this->factory->createFromArray($options);

        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }
}
