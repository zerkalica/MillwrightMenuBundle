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

use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

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
    protected $defaultrouteParameters = array();

    /**
     * Per item route params
     *
     * @var array
     */
    protected $routeParameters = array();

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

    protected $aclProvider;

    /**
     * @var array
     */
    protected $extra = array();

    public function __construct(
        RouterInterface $router,
        SecurityContextInterface $security,
        AclProviderInterface $aclProvider = null
    )
    {
        $this->router      = $router;
        $this->security    = $security;
        $this->aclProvider = $aclProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::setDefaultrouteParameters()
     */
    public function setDefaultrouteParameters(array $defaultrouteParameters)
    {
        $this->defaultrouteParameters = $defaultrouteParameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see Millwright\MenuBundle\Menu.MenuFactoryInterface::setrouteParameters()
     */
    public function setrouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultExtraParameters(array $extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Set uri, display context to menu item
     *
     * @param  MenuItemInterface $item
     * @param  array             $routeParameters
     * @param  array             $options
     *
     * @return MenuFactoryInterface
     */
    protected function setContext(MenuItemInterface $item,
        array $routeParameters = array(),
        array $options = array())
    {
        $display  = true;
        $rootItem = !$item->getName();
        $token    = $this->security->getToken();

        if ($token) {
            if ($options['roles'] && !$this->security->isGranted($options['roles'])) {
                $display = false;
            }


            if ($display) {
                foreach ((array) $item->getExtra('oids') as $oidItem) {
                    if (!$this->security->isGranted($oidItem['permissions'], $oidItem['oid'])) {
                        $display = false;
                        break;
                    }
                }
            }
        }

        if ($options['route'] && !$rootItem) {
            $acceptedRouteParameters = array_intersect_key($routeParameters, $options['routeAcceptedParameters']);

            //@todo refactor this compare logic, do routeAcceptedParameters and routeRequiredParameters to same format
            if ($options['routeRequiredParameters'] === array_keys($acceptedRouteParameters)) {
                $uri = $this->router->generate(
                    $options['route'],
                    $acceptedRouteParameters,
                    $options['routeAbsolute']
                );
                $item->setUri($uri);
            } else {
                $display = false;
            }
        }

        if (!$display) {
            if ($options['showNonAuthorized'] && !$token) {
                $display = true;
            }
            if ($options['showAsText']) {
                $display = true;
                $item->setUri(null);
            }
        }

        if (!$display) {
            $item->setDisplay(false);
        }

        return $this;
    }

    /**
     * Create new empty instance of menu item
     *
     * @param  string $name
     *
     * @return ItemInterface
     */
    protected function createItemInstance($name)
    {
        return new MenuItem($name, $this);
    }

    /**
     * {@inheritdoc}
     *
     * @see Knp\Menu.FactoryInterface::createItem()
     *
     * @param array & $itemList    array(MenuItemInterface $menuItem, array $routeParameters, array $options)
     * @param array & $preloadOids preloaded oids for AclProviderInterface::findAcls()
     *
     * @return MenuItemInterface
     */
    public function createItem($name, array $options = array(), array & $itemList = array(),
        array & $preloadOids = array())
    {
        $options += array(
            'uri'                     => null,
            'label'                   => null,
            'attributes'              => array(),
            'linkAttributes'          => array(),
            'childrenAttributes'      => array(),
            'labelAttributes'         => array(),
            'display'                 => true,
            'displayChildren'         => true,

            'type'                    => null,
            'translateDomain'         => null,
            'translateParameters'     => array(),

            'roles'                   => array(),
            'secureParams'            => array(),
            'route'                   => null,
            'routeAbsolute'           => false,
            'routeParameters'         => array(),
            'routeAcceptedParameters' => array(),
            'routeRequiredParameters' => array(),
            'showNonAuthorized'       => false,
            'showAsText'              => false,
        );

        $item = $this->createItemInstance($name);

        $this->routeParameters += array($name => array());

        $routeParameters = array_merge($options['routeParameters'], $this->defaultrouteParameters, $this->routeParameters[$name]);

        $itemOids = array();

        if (!empty($options['secureParams'])) {

            foreach ($options['secureParams'] as $secureParam) {
                $paramName = $secureParam['name'];
                if (isset($routeParameters[$paramName])) {
                    $permissions   = $secureParam['permissions'];
                    $entityId      = $routeParameters[$paramName];
                    $entityClass   = $secureParam['class'];
                    $oid           = new ObjectIdentity($entityId, $entityClass);
                    $itemOids[]    = array('permissions' => $permissions, 'oid' => $oid);
                    $preloadOids[] = $oid;
                }
            }
        }

        $extra = array(
            'type'                => $options['type'],
            'translateDomain'     => $options['translateDomain'],
            'translateParameters' => $options['translateParameters'],
            'oids'                => $itemOids,
        );

        $extra = array_merge($extra, $this->extra);

        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
            ->setExtras($extra);

        $itemList[] = array($item, $routeParameters, $options);

        return $item;
    }

    /**
     * {@inheritdoc}
     *
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
     *
     * @see Knp\Menu.FactoryInterface::createFromArray()
     */
    public function createFromArray(array $data, $name = null)
    {
        $preloadOids = $itemList = array();
        $menu        = $this->_createFromArray($data, $name, $itemList, $preloadOids);

        if ($preloadOids && $this->aclProvider) {
            //preload all acls for menu items
            try {
                $this->aclProvider->findAcls($preloadOids);
            } catch(AclNotFoundException $e) {
            }
        }

        foreach ($itemList as $item) {
            $this->setContext($item[0], $item[1], $item[2]);
        }

        return $menu;
    }

    /**
     * @param array                                 $data
     * @param string|null                           $name
     * @param MenuItemInterface[]                   & $itemList plain list of menu items
     * @param ObjectIdentityInterface[]             & $preloadOids
     *
     * @return \Knp\Menu\ItemInterface
     */
    private function _createFromArray(array $data, $name = null, array & $itemList, array & $preloadOids)
    {
        $item = $this->createItem($name, $data, $itemList, $preloadOids);
        foreach ($data['children'] as $name => $child) {
            $subItem = $this->_createFromArray($child, $name, $itemList, $preloadOids);
            $item->addChild($subItem);
        }

        return $item;
    }
}
