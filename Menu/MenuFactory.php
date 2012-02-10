<?php
/**
 * Menu factory, supports menu context
 *
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */

namespace Millwright\MenuBundle\Menu;

use Knp\Menu\NodeInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var MenuContextInterface
     */
    protected $context;

    /**
     * @var array
     */
    protected $defaultRouteParams = array();

    /**
     * @var array
     */
    protected $routeParams = array();

    public function __construct(MenuContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Create new empty instance of menu item
     *
     * @param  string $name
     * @return ItemInterface
     */
    protected function createItemInstance($name)
    {
        return new MenuItem($name, $this);
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::setDefaultRouteParams()
     */
    public function setDefaultRouteParams(array $defaultRouteParams)
    {
        $this->defaultRouteParams = $defaultRouteParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::setRouteParams()
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createItem()
     *
     * @return MenuItemInterface
     */
    public function createItem($name, array $options = array())
    {
        $item    = $this->createItemInstance($name);

        if ($name) {
            unset($options['children']);
            foreach ($options as $key => $default) {
                $method = 'set' . ucfirst($key);
                $item->$method($options[$key]);
            }
        }

        $params = isset($this->routeParams[$name])
            ? $this->routeParams[$name]
            : $this->defaultRouteParams;

        $this->context->setContext($item, $params);

        return $item;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createFromNode()
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions());
        /* @var $childNode NodeInterface */
        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createFromArray()
     */
    public function createFromArray(array $data)
    {
        $item = $this->createItem($data['name'], $data);
        foreach ($data['children'] as $key => $child) {
            $item->addChild($this->createFromArray($child));
        }

        return $item;
    }
}
