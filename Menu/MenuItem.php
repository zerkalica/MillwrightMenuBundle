<?php
/**
 * Menu item class
 *
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
namespace Millwright\MenuBundle\Menu;
use Knp\Menu\MenuItem as KnpMenuItem;

/**
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
class MenuItem extends KnpMenuItem
{
    /**
     * {@inheritdoc}
     * @see Knp\Menu.MenuItem::addChild()
     *
     * Our menu items created by menu factory and $child always MenuItemInterface.
     * Factory sets current uri and other variable parts of menu item options
     * We don't need to set it here
     */
    public function addChild($child, array $options = array())
    {
        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }
}
