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
class MenuContext implements MenuContextInterface
{
    /**
     * @var string
     */
    private $requestUri;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SecurityContextInterface
     */
    private $security;

    public function __construct(
        Request                  $request,
        RouterInterface          $router,
        SecurityContextInterface $security
    ) {
        $this->requestUri  = $request->getRequestUri();
        $this->router      = $router;
        $this->security    = $security;
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

        $item->setDisplay($display);
        $item->setCurrentUri($this->requestUri);

        if ($recursive) {
            foreach($item->getChildren() as $child) {
                $this->setContext($child, $routeParameters, $recursive);
            }
        }

        return $this;
    }
}
