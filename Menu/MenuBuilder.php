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
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Create menu
     *
     * @param  Request $request
     * @param  array $options
     * @return ItemInterface
     */
    public function createMenu(Request $request, array $options = array())
    {
        $menu = $this->factory->createFromArray($options);
        $menu->setCurrentUri($request->getRequestUri());

        return $menu;
    }
}
