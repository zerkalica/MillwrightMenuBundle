<?php
namespace Millwright\MenuBundle\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Millwright menu twig renderer
 */
class TwigRenderer implements RendererInterface
{
    protected $environment;
    protected $defaultOptions;
    protected $matcher;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct(
        \Twig_Environment $environment,
        $template,
        MatcherInterface $matcher,
        array $defaultOptions = array()
    ) {
        $this->environment    = $environment;
        $this->matcher        = $matcher;
        $this->defaultOptions = array_merge(array(
            'block'             => 'root',
            'depth'             => null,
            'currentAsLink'     => true,
            'currentClass'      => 'current',
            'ancestorClass'     => 'current_ancestor',
            'firstClass'        => 'first',
            'lastClass'         => 'last',
            'template'          => $template,
            'compressed'        => false,
            'allow_safe_labels' => false,
        ), $defaultOptions);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface $item
     * @param array         $options
     *
     * @return string
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $template = $options['template'];
        if (!$template instanceof \Twig_Template) {
            $template = $this->environment->loadTemplate($template);
        }

        $data = $this->getData($item);

        $html = $template->renderBlock($options['block'], array(
            'item'    => $data,
            'options' => $options,
            'matcher' => $this->matcher
        ));

        if (!empty($options['clear_matcher'])) {
            $this->matcher->clear();
        }

        return $html;
    }

    /**
     * Get data for template by menu item
     * used for breadcrumb
     *
     * @param ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    protected function getData(ItemInterface $item)
    {
        return $item;
    }
}
