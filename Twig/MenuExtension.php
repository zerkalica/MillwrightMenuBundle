<?php

namespace Millwright\MenuBundle\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class MenuExtension extends \Twig_Extension
{
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions()
    {
        return array(
            'millwright_menu_get' => new \Twig_Function_Method($this, 'get'),
            'millwright_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @return \Knp\Menu\ItemInterface
     */
    public function get($menu, array $path = array())
    {
        return $this->helper->get($menu, $path);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param array $routeParams
     * @param array $options
     * @param string $renderer
     * @return string
     */
    public function render($menu, array $routeParams = array(), array $options = array(), $renderer = null)
    {
        return $this->helper->render($menu, $routeParams, $options, $renderer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'millwright_menu';
    }
}
