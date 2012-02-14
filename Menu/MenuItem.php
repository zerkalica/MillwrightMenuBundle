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
class MenuItem extends KnpMenuItem implements MenuItemInterface
{
    /**
     * @var string
     */
    protected $translateDomain;

    /**
     * @var array
     */
    protected $translateParameters = array();

    /**
     * @var string
     */
    protected $type;

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setTranslateDomain()
     */
    public function setTranslateDomain($translateDomain)
    {
        $this->translateDomain = $translateDomain;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getTranslateDomain()
     */
    public function getTranslateDomain()
    {
        return $this->translateDomain;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setTranslateParameters()
     */
    public function setTranslateParameters(array $translateParameters)
    {
        $this->translateParameters = $translateParameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getTranslateParameters()
     */
    public function getTranslateParameters()
    {
        return $this->translateParameters;
    }

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
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setType()
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getType()
     */
    public function getType()
    {
        return $this->type;
    }
}
