<?php
/**
 * Translate domain interface added to Knp ItemInterface
 *
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
namespace Millwright\MenuBundle\Menu;

use Knp\Menu\ItemInterface;

/**
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
interface MenuItemInterface extends ItemInterface
{
    /**
     * Get translate domain
     *
     * @return string
     */
    public function getTranslateDomain();

    /**
     * Set translate domain
     *
     * @param  string $translateDomain
     * @return MenuItemInteface
     */
    public function setTranslateDomain($translateDomain);

    /**
     * Set translateParameters
     *
     * @param  array $translateParameters
     * @return MenuItemInteface
     */
    public function setTranslateParameters(array $translateParameters);

    /**
     * Get translateParameters
     *
     * @return array
     */
    public function getTranslateParameters();

    /**
     * Set menu item type
     *
     * @param  string $type
     * @return MenuItemInterface
     */
    public function setType($type);

    /**
     * Get menu item type
     *
     * @return string
     */
    public function getType();
}
