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

    /**
     * {@inheritdoc}
     * @param  null $subItem
     * @return array
     */
    public function getBreadcrumbsArray($subItem = null)
    {
        $breadcrumbs = array();
        $obj = $this;

        if ($subItem) {
            if (!is_array($subItem)) {
                $subItem = array((string) $subItem => null);
            }
            $subItem = array_reverse($subItem);
            foreach ($subItem as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                    $value = null;
                }
                $breadcrumbs[(string) $key] = $value;
            }
        }

        do {
            $name = $obj->getName();
            $breadcrumbs[$name] = $obj;
        } while ($obj = $obj->getParent());

        return array_reverse($breadcrumbs, true);
    }
}
