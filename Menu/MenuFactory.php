<?php
/**
 * Menu factory, support security context and routes
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
    private $context;

    /**
     * @var array
     */
    private $routeParams = array();

    /**
     * @var string
     */
    private $currentUri;

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

    private function getDefaultParams()
    {
        return array(
            'uri'                => null,
            'label'              => null,
            'name'               => null,
            'attributes'         => array(),
            'linkAttributes'     => array(),
            'childrenAttributes' => array(),
            'labelAttributes'    => array(),
            'display'            => true,
            'displayChildren'    => true,

            'translateDomain'    => null,
            'translateParameters' => array(),
            'secureParams'        => array(),
            'roles'               => array(),
            'route'               => null,
            'routeAbsolute'       => false,
        );
    }

    /**
     * {@inheritdoc}
     * @see MenuFactoryInterface::setRouteParams()
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }


    /**
     * {@inheritdoc}
     * @see MenuFactoryInterface::setRouteParams()
     */
    public function setCurrentUri($currentUri)
    {
        $this->currentUri = $currentUri;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createItem()
     */
    public function createItem($name, array $options = array())
    {
        $item    = $this->createItemInstance($name);

        if ($name) {
            foreach ($options as $key => $default) {
                $method = 'set' . ucfirst($key);
                $item->$method($options[$key]);
            }
        }

        $this->context->setContext($item, $this->routeParams);

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
