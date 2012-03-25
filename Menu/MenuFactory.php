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
use Knp\Menu\ItemInterface as MenuItemInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\SecurityContextInterface;


/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuFactory implements MenuFactoryInterface
{
    /**
     * Per menu route params
     *
     * @var array
     */
    protected $defaultRouteParams = array();

    /**
     * Per item route params
     *
     * @var array
     */
    protected $routeParams = array();

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

    /**
     * @var string
     */
    protected $currentUri;

    public function __construct(
        RouterInterface          $router,
        SecurityContextInterface $security
    ) {
        $this->router      = $router;
        $this->security    = $security;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::setCurrentUri()
     */
    public function setCurrentUri($currentUri)
    {
        $this->currentUri = $currentUri;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::getCurrentUri()
     */
    public function getCurrentUri()
    {
        return $this->currentUri;
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
     * Set uri, display, current uri to menu item
     *
     * @param  MenuItemInterface $item
     * @param  array $routeParameters
     * @param  array $options
     * @return MenuFactoryInterface
     */
    protected  function setContext(MenuItemInterface $item,
        array $routeParameters = array(),
        array $options = array())
    {
        $display = true;
        $rootItem = !$item->getName();

        $token = $this->security->getToken();

        if ($token) {
            if($options['roles'] && !$this->security->isGranted($options['roles'])) {
                $display = false;
            }

            if ($display) {
                foreach($options['secureParams'] as $secureParam) {
                    $paramName   = $secureParam['name'];
                    if(isset($routeParameters[$paramName])) {
                        $permissions = $secureParam['permissions'];
                        $entityId    = $routeParameters[$paramName];
                        $entityClass = $secureParam['class'];
                        $object      = new ObjectIdentity($entityId, $entityClass);
                        var_export($object);
                        if(!$this->security->isGranted($permissions, $object)) {
                            $display = false;
                            break;
                        }
                    }
                }
            }
        }

        if ($options['route'] && !$rootItem) {
            $uri = $this->router->generate(
                $options['route'],
                $routeParameters,
                $options['routeAbsolute']
            );
            $item->setUri($uri);
        }

        if(!$display) {
            if ($options['showNonAuthorized'] && !$token) {
                $display = true;
            }
            if ($options['showAsText']) {
                $display = true;
                $item->setUri(null);
            }
        }

        if(!$display) {
            $item->setDisplay(false);
        }

        $item->setCurrentUri($this->currentUri);

        return $this;
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
     * @see Knp\Menu.FactoryInterface::createItem()
     *
     * @return MenuItemInterface
     */
    public function createItem($name, array $options = array())
    {
        $options += array(
            'uri' => null,
            'label' => null,
            'attributes' => array(),
            'linkAttributes' => array(),
            'childrenAttributes' => array(),
            'labelAttributes' => array(),
            'display' => true,
            'displayChildren' => true,

            'type' => null,
            'translateDomain' => null,
            'translateParamteters' => array(),

            'roles' => array(),
            'secureParams' => array(),
            'route' => null,
            'routeAbsolute' => false,
            'showNonAuthorized' => false,
            'showAsText' => false,
        );

        $item = $this->createItemInstance($name);

        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
            ->setExtras(array(
                'type' => $options['type'],
                'translateDomain' => $options['translateDomain'],
                'translateParameters' => $options['translateParameters']
            ))
        ;

        $params = isset($this->routeParams[$name])
            ? $this->routeParams[$name]
            : $this->defaultRouteParams;

        $this->setContext($item, $params, $options);

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
    public function createFromArray(array $data, $name = null)
    {
        $item = $this->createItem($name, $data);
        foreach ($data['children'] as $name => $child) {
            $item->addChild($this->createFromArray($child, $name));
        }

        return $item;
    }
}
