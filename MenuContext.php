<?php

namespace Millwright\MenuBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Security\Core\SecurityContext;

use Millwright\MenuBundle\MenuItem;

class MenuContext
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * Sets initial values
     */
    public function __construct(Router $router, Request $request, Translator $translator, SecurityContext $securityContext)
    {
        $this->router          = $router;
        $this->request         = $request;
        $this->translator      = $translator;
        $this->securityContext = $securityContext;

    }

    /**
     * Create new instance of menu
     *
     * @return \Millwright\MenuBundle\Menu
     */
    public function create($name = null)
    {
        $menu = new Menu($this, null, null, $name);

        $this->setupMenuItem($menu);

        return $menu;
    }

    /**
     * @param array $options
     * @param unknown_type $name
     * @return \Millwright\MenuBundle\Menu
     */
    public function createFromArray(array $options, $name = null)
    {
        return $this->create($name)->fromArray($options);
    }

    /**
     * Setup context of menu item
     *
     * @param  MenuItem $item
     * @return \Millwright\MenuBundle\Menu
     */
    public function setupMenuItem(MenuItem $item)
    {
        $item->setMenuContext($this);
        $item->setCurrentUri($this->request->getRequestUri());

        if (!$item->getUri() && $item->getRoute()) {
            $item->setUri($this->router->generate($item->getRoute(), $item->getRouteParams(), $item->getAbsolute()));
        }
        if ($item->getDomain()) {
            $item->setLabel($this->translator->trans($item->getLabel(), $item->getTranslateParams(), $item->getDomain()));
        }
        if ($item->getRoles()) {
            $item->setShow($this->securityContext->isGranted($item->getRoles()));
        }

        return $this;
    }
}
