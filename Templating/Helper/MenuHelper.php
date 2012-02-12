<?php

namespace Millwright\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper as TemplatingHelper;
use Millwright\MenuBundle\Twig\Helper;

class MenuHelper extends TemplatingHelper
{
    private $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
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
