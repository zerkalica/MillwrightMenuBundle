<?php
/**
 * Translate domain interface added to Knp ItemInterface
 *
 * @author   Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 */
namespace Millwright\MenuBundle\Menu;

use Knp\Menu\ItemInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
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
     * Get routeAbsolute
     *
     * @return bool
     */
    public function getRouteAbsolute();

    /**
     * Set secureParams
     *
     * @param  array $secureParams
     * @return MenuItemInteface
     */
    public function setSecureParams(array $secureParams);

    /**
     * Get secureParams
     *
     * @return array
     */
    public function getSecureParams();
}
