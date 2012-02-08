<?php
/**
 * Menu builder
 *
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */

namespace Millwright\MenuBundle\Menu;

use Knp\Menu\MenuItem;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;

use Millwright\MenuBundle\Config\OptionMergerInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SecurityContextInterface
     */
    private $security;

    /**
     * @var OptionMergerInterface
     */
    private $merger;

    /**
     * @var array
     */
    private $menuOptions;

    public function __construct(
        FactoryInterface         $factory,
        RouterInterface          $router,
        SecurityContextInterface $security,
        OptionMergerInterface    $merger,
        array                    $menuOptions
    ) {
        $this->factory     = $factory;
        $this->router      = $router;
        $this->security    = $security;
        $this->merger      = $merger;
        $this->menuOptions = $this->normalize($menuOptions);
    }

    protected function normalize(array $menuOptions)
    {
        foreach($menuOptions as & $menu) {
            $menu = $this->_merge($menu);
        }

        return $menuOptions;
    }

    protected function _merge(array $options, array $parentOptions = array())
    {
        $options += array('name' => null);
        if (!empty($options['name'])) {
            $options = $this->merger->merge($options, $parentOptions);
        } else {
            $options['name'] = null;
        }

        foreach($options['children'] as $name => & $child) {
            $child += array('name' => $name);
            $child = $this->_merge($child, $options);
        }

        return $options;
    }

    /**
     * Get menu by name
     *
     * @todo add caching
     *
     * @param  string $name
     * @return MenuItemInterface
     */
    protected function getMenu($name)
    {
        $options = $this->menuOptions[$name];

        return $this->factory->createFromArray($options);
    }

    /**
     * Create menu
     *
     * @param  string  $name
     * @param  Request $request
     * @param  array   $routeParams
     * @return ItemInterface
     */
    public function createMenu($name, Request $request, array $routeParams = array())
    {
        $menu = $this->getMenu($name);

        $menu->setCurrentUri($request->getRequestUri());
        $this->setContext($menu, $routeParams, true);

        return $menu;
    }

    /**
     * Get entity by class name and id
     *
     * @param  string $entityClass
     * @param  int $entityId
     * @return Object
     */
    private function getObject($entityClass, $entityId)
    {
        return $this->em->find($entityClass, $entityId);
    }

    /**
     * Set variable context: route parameters and load security visibility
     *
     * @todo improve caching by route name, route params, user
     *
     * @param  MenuItemInterface $item
     * @param  array $routeParameters
     * @param  boolean|false $recursive if true - set route parameters to child items
     * @return MenuFactory
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
                $object = null;
                if(isset($routeParameters[$paramName])) {
                    $entityId    = $routeParameters[$paramName];
                    $entityClass = $secureParam['class'];

                    //@todo howto get grant status without fetching object
                    // only by class name and Id ?
                    // howto cache isGranted result ?
                    $object = $this->getObject($entityClass, $entityId);
                }
                if($object && !$this->security->isGranted($secureParam['permissions'], $object)) {
                    $display = false;
                    break;
                }
            }
        }

        $uri   = null;
        $route = $item->getRoute();
        if ($route) {
            $uri = $this->router->generate(
                $route,
                $routeParameters,
                $item->getRouteAbsolute()
            );
        }

        $item->setUri($uri);
        $item->setDisplay($display);

        if ($recursive) {
            foreach($item->getChildren() as $child) {
                $this->setContext($child, $routeParameters, $recursive);
            }
        }

        return $this;
    }
}
