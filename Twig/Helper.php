<?php

namespace Millwright\MenuBundle\Twig;

use Millwright\MenuBundle\Renderer\RendererOptionsInterface;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Millwright\MenuBundle\Menu\MenuBuilderInterface;
use Millwright\MenuBundle\Menu\MenuItemInterface;

/**
 * Helper class containing logic to retrieve and render menus from templating engines
 *
 */
class Helper
{
    protected $rendererProvider;
    protected $builder;
    protected $rendererOptions;

    /**
     * @param RendererProviderInterface $rendererProvider
     * @param MenubuilderInterface $builder
     * @param RendererOptionsInterface $rendererOptions
     */
    public function __construct(RendererProviderInterface $rendererProvider, MenuBuilderInterface $builder, array $rendererOptions)
    {
        $this->rendererProvider = $rendererProvider;
        $this->builder          = $builder;
        $this->rendererOptions  = $rendererOptions;
    }

    /**
     * Retrieves item in the menu, eventually using the menu provider.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @param array $routeParams
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException when the path is invalid
     * @throws \BadMethodCallException when there is no menu provider and the menu is given by name
     */
    public function get($menu, array $path = array(), array $defaultRouteParams = array())
    {
        if (!$menu instanceof MenuItemInterface) {
            if (null === $this->builder) {
                throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
            }

            $menuName = $menu;

            $routeParams = array();
            if(isset($defaultRouteParams['_default'])) {
                $routeParams = $defaultRouteParams;
                unset($routeParams['_default']);
                $defaultRouteParams =  $defaultRouteParams['_default'];
            }

            $menu = $this->builder->createMenu($menuName, $defaultRouteParams, $routeParams);

            if (!$menu instanceof ItemInterface) {
                throw new \LogicException(sprintf('The menu "%s" exists, but is not a valid menu item object. Check where you created the menu to be sure it returns an ItemInterface object.', $menuName));
            }
        }

        foreach ($path as $child) {
            $menu = $menu->getChild($child);
            if (null === $menu) {
                throw new \InvalidArgumentException(sprintf('The menu has no child named "%s"', $child));
            }
        }

        return $menu;
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * If the argument is an array, it will follow the path in the tree to
     * get the needed item. The first element of the array is the whole menu.
     * If the menu is a string instead of an ItemInterface, the provider
     * will be used.
     *
     * @throws \InvalidArgumentException
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param array $routeParams
     * @param array $options
     * @param string $renderer
     * @return string
     */
    public function render($menu, array $routeParams = array(), array $options = array(), $renderer =  null)
    {
        if (!$menu instanceof MenuItemInterface) {
            $path = array();
            if (is_array($menu)) {
                if (empty($menu)) {
                    throw new \InvalidArgumentException('The array cannot be empty');
                }
                $path = $menu;
                $menu = array_shift($path);
            }

            $menu = $this->get($menu, $path, $routeParams);
        }


        $type = $menu->getType();
        if($type && isset($this->rendererOptions[$type])) {
            $rendererParams = $this->rendererOptions[$type];
            $options += $rendererParams['rendererOptions'];
            if ($renderer === null) {
                $renderer = $rendererParams['renderer'];
            }
        }

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }
}
