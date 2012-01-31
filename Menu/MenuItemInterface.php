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
     * @return MenuItem
     */
    public function setTranslateParameters(array $translateParameters);

    /**
     * Get translateParameters
     *
     * @return array
     */
    public function getTranslateParameters();

}
