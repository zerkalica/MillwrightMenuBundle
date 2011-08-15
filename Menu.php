<?php
/**
 * @author Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 * @package  MenuBundle
 */
namespace Millwright\MenuBundle;
use Symfony\Component\Routing\RouteCollection;
use Knp\Bundle\MenuBundle\Menu as KnpMenu;


use Millwright\MenuBundle\MenuContext;

/**
 * @author Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 * @package  MenuBundle
 */
class Menu extends MenuItem
{
    protected $childClass = '\Millwright\MenuBundle\MenuItem';

    /**
     * Constructor
     *
     * @param MenuContext $menuContext
     * @param array   $menuOptions
     */
    public function __construct(
        MenuContext $menuContext = null,
        $menuOptions = null,
        $attributes = null,
        $name = null
    )
    {
        parent::__construct();

        $menuContext->setupMenuItem($this);

        if ($attributes) {
            $this->setAttributes($attributes);
        }

        if ($menuOptions) {
            $this->fromArray($menuOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($menuOptions)
    {
        $menuOptions = $this->normalizeArray($menuOptions);
        parent::fromArray($menuOptions);

        return $this;
    }

    public function initialize(array $options = array())
    {
    }

    /**
     * Convert menu item route and params to uri
     *
     * @param  string $name
     * @param  array $child
     * @return array
     */
    protected function normalizeItem($id, array $child)
    {
        $child += array(
            'name'  => $id,
        );

        return $child;
    }

    /**
     * Normalize all menu items
     *
     * @param  array $item
     * @return array
     */
    protected function normalizeArray(array $item)
    {
        $item['submenu'] = $this->_normalizeArray($item['submenu']);

        return $item;
    }

    /**
     * @see    Menu::normalizeArray()
     * @param  array $items
     * @return array
     */
    protected function _normalizeArray(array $items)
    {
        foreach ($items as $id => &$item) {
            $item = $this->normalizeItem($id, $item);
            if (!empty($item['submenu'])) {
                $item['submenu'] = $this->_normalizeArray($item['submenu']);
            }
        }

        return $items;
    }
}
