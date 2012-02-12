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
     * Set roles
     *
     * @param  array $roles
     * @return MenuItemInteface
     */
    public function setRoles(array $roles);

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles();

    /**
     * Set route name
     *
     * @param  string $route
     * @return MenuItemInteface
     */
    public function setRoute($route);

    /**
     * Get route name
     *
     * @return string
     */
    public function getRoute();

    /**
     * Is route absolute ?
     *
     * @param  bool $routeAbsolute
     * @return MenuItemInteface
     */
    public function setRouteAbsolute($routeAbsolute);

    /**
     * Get route absolute
     *
     * @return bool
     */
    public function getRouteAbsolute();

    /**
     * Set secure params
     *
     * @param  array $secureParams
     * @return MenuItemInteface
     */
    public function setSecureParams(array $secureParams);

    /**
     * Get secure params
     *
     * @return array
     */
    public function getSecureParams();

    /**
     * Set show non authorized
     * If access denied and not authorized - show link
     *
     * @param  bool $showNonAuthorized
     * @return MenuItem
     */
    public function setShowNonAuthorized($showNonAuthorized);

    /**
     * Get show non authorized state
     *
     * @return bool
     */
    public function getShowNonAuthorized();

    /**
     * Set show as text
     * if access denied - show text
     *
     * @param  bool $showAsText
     * @return MenuItemInterface
     */
    public function setShowAsText($showAsText);

    /**
     * Get show as text state
     *
     * @return bool
     */
    public function getShowAsText();

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
