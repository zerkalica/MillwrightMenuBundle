<?php
/**
 * Menu factory interface
 *
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
namespace Millwright\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;

/**
 * @author     Stefan Zerkalica <zerkalica@gmail.com>
 * @category   Millwright
 * @package    MenuBundle
 * @subpackage Menu
 */
interface MenuFactoryInterface extends FactoryInterface
{

    /**
     * Set route params
     * This parameters array for each menu item.
     * Overlaps $defaultRouteParams
     *
     * @param  array $routeParams
     * @example
     *     <code>
     *         $routeParams = array(
     *             'fos_user_change_password' => array(
     *                 'id' => '1',
     *             ),
     *         );
     *     </code>
     * @return MenuFactoryInterface
     */
    public function setRouteParameters(array $routeParams);

    /**
     * Set default route parameters
     * This parameters will be used for all menu items in container
     * if route parameters per item is not set
     *
     * @param  array $defaultRouteParams
     * @example
     *     <code>
     *         $defaultRouteParams = array(
     *             'userId' => 1,
     *             'nodeId' => 2,
     *         );
     *     </code>
     * @return MenuFactoryInterface
     */
    public function setDefaultRouteParameters(array $defaultRouteParams);

    /**
     * Set extra parameters, the will set to all items on menu creation
     *
     * @param array $extra
     *
     * @example
     *     <code>
     *         $extra = array(
     *             'translationParameters' => '',
     *             'translationDomain' => '',
     *         );
     *     </code>
     *
     * @return MenuFactoryInterface
     */
    public function setDefaultExtraParameters(array $extra);
}
