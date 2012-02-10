<?php
/**
 * @author   Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 */
namespace Millwright\MenuBundle\Menu;

use Knp\Menu\MenuItem as KnpMenuItem;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
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
    protected $translateParameters;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var bool
     */
    protected $routeAbsolute;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var array
     */
    protected $secureParams;

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setSecureParams()
     */
    public function setSecureParams(array $secureParams)
    {
        $this->secureParams = $secureParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getSecureParams()
     */
    public function getSecureParams()
    {
        return $this->secureParams;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setRoles()
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getRoles()
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setRouteAbsolute()
     */
    public function setRouteAbsolute($routeAbsolute)
    {
        $this->routeAbsolute = $routeAbsolute;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getRouteAbsolute()
     */
    public function getRouteAbsolute()
    {
        return $this->routeAbsolute;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::setRoute()
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuItemInterface::getRoute()
     */
    public function getRoute()
    {
        return $this->route;
    }

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

}
