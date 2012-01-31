<?php
/**
 * @author   Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 */
namespace Millwright\MenuBundle\Menu;

use Knp\Menu\MenuItem as KnpMenuItem;
use Knp\Menu\ItemInterface;
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
}
