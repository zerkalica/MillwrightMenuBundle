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

use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\NodeInterface;

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
     * @var array
     */
    protected $defaultRouteParams = array();

    /**
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
        SecurityContextInterface $security,
        ContainerInterface       $container
        //Request                  $request
    ) {
        $this->router      = $router;
        $this->security    = $security;
        //@todo fix this
        $this->currentUri  = $container->get('request')->getRequestUri();
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
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::setContext()
     */
    public function setContext(MenuItemInterface $item,
        array $routeParameters = array(),
        $recursive = false)
    {
        $display = true;

        $roles = $item->getRoles();
        if($roles && !$this->security->isGranted($roles)) {
            $display = false;
        }

        $secureParams = $item->getSecureParams();
        if ($display && $secureParams) {
            foreach($secureParams as $secureParam) {
                $paramName   = $secureParam['name'];
                if(isset($routeParameters[$paramName])) {
                    $permissions = $secureParam['permissions'];
                    $entityId    = $routeParameters[$paramName];
                    $entityClass = $secureParam['class'];
                    $object      = new ObjectIdentity($entityId, $entityClass);

                    if(!$this->security->isGranted($permissions, $object)) {
                        $display = false;
                        break;
                    }
                }
            }
        }

        $route = $item->getRoute();
        if ($route) {
            $uri = $this->router->generate(
                $route,
                $routeParameters,
                $item->getRouteAbsolute()
            );
            $item->setUri($uri);
        }

        if(!$display ) {
            if ($item->getShowNonAuthorized() && !$this->security->getToken()) {
                $display = true;
            } else if($item->getShowAsText()) {
                $display = true;
                $item->setUri(null);
            }
        }

        $item->setDisplay($display);
        $item->setCurrentUri($this->currentUri);

        if ($recursive) {
            foreach($item->getChildren() as $child) {
                $this->setContext($child, $routeParameters, $recursive);
            }
        }

        return $this;
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
        $item = $this->createItemInstance($name);

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

        $this->setContext($item, $params);

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
