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

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuFactory implements FactoryInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

    public function __construct(
        UrlGeneratorInterface $generator,
        SecurityContextInterface $security)
    {
        $this->generator = $generator;
        $this->security  = $security;
    }

    /**
     * This params will be copied to MenuItem object
     *
     * @return array [string]
     */
    protected function getDefaultParams()
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
            'translateParameters'    => array(),
        );
    }

    /**
     * Get default values for menu item options array normalization
     *
     * @return array
     */
    protected function getNormalizeParams()
    {
        return array_merge($this->getDefaultParams(), array(
            'route'           => null,
            'routeParameters' => array(),
            'routeAbsolute'   => false,
            'role'            => array(),
        ));
    }

    /**
     * This params will be copied from parent to child item
     * if not set in child
     *
     * @return array
     */
    protected function getInheritedParams()
    {
        return array(
            'translateDomain' => null,
            'routeParameters' => array(),
            'role'            => array(),
        );
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
     * Set generic menu item options from array
     *
     * @param  MenuItemInterface $menu
     * @param  array $options
     * @return FactoryInterface
     */
    private function setOptions(MenuItemInterface $menu, array $options)
    {
        foreach ($this->getDefaultParams() as $key => $default) {
            $value = isset($options[$key]) ? $options[$key] : $default;
            $method = 'set' . ucfirst($key);
            $menu->$method($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createItem()
     */
    public function createItem($name, array $options = array())
    {
        $options += $this->getNormalizeParams();
        $item    = $this->createItemInstance($name);

        if ($options['route']) {
            $options['uri'] = $this->generator->generate(
                $options['route'],
                $options['routeParameters'],
                $options['routeAbsolute']
            );
        }

        $this->setOptions($item, $options);

        $role = $options['role'];

        if($name && $role && !$this->security->isGranted($role)) {
            $item->setDisplay(false);
        }

        return $item;
    }

    /**
     * Copy some values from $options to $childOptions,
     * if not set in destination
     *
     * @see MenuFactory::getInheritedParams()
     *
     * @param  array $options source
     * @param  array $childOptions destination
     *
     * @return array result
     */
    private function inheritOptions(array $options, array $childOptions)
    {
        foreach($this->getInheritedParams() as $param => $value) {
            if(isset($options[$param])) {
                $value = $options[$param];
            }
            if(empty($childOptions[$param])) {
                $childOptions[$param] = $value;
            }
        }

        return $childOptions;
    }

    /**
     * {@inheritdoc}
     * @see Knp\Menu.FactoryInterface::createFromNode()
     */
    public function createFromNode(NodeInterface $node)
    {
        $options = $node->getOptions();
        $item    = $this->createItem($node->getName(), $options);

        /* @var $childNode NodeInterface */
        foreach ($node->getChildren() as $childNode) {
            $childNode->setOptions($this->inheritOptions(
                $options, $childNode->getOptions()
            ));

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
        $data += array('name' => null);
        $name = $data['name'];

        if(!isset($data['route'])) {
            $data['route'] = $name;
        }

        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = array();
        }

        $item = $this->createItem($name, $data);
        foreach ($children as $name => $child) {
            $child += array('name' => $name);
            $child = $this->inheritOptions($data, $child);
            $item->addChild($this->createFromArray($child));
        }

        return $item;
    }
}
