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
    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $defaultOptions;
    private $matcher;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct(\Twig_Environment $environment, $template, MatcherInterface $matcher,
        array $defaultOptions = array())
    {
        $this->environment    = $environment;
        $this->matcher        = $matcher;
        $this->defaultOptions = array_merge(array(
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

        $block = isset($options['block']) ? $options['block'] : 'root';
        //@todo remove breadcrumb into config
        if ($block == 'breadcrumb') {
            $data  = $this->getCurrentItem($item->getRoot())->getBreadcrumbsArray();
        } else {
            $data = $item;
        }

        $html = $template->renderBlock($block, array('item' => $data, 'options' => $options, 'matcher' => $this->matcher));

        if (!empty($options['clear_matcher'])) {
            $this->matcher->clear();
        }

        return $html;
    }

    /**
     * Get current item
     *
     * @param ItemInterface $item
     *
     * @return ItemInterface|null
     */
    protected function getCurrentItem(ItemInterface $item)
    {
        foreach ($item->getChildren() as $child) {
            if ($this->matcher->isCurrent($child)) {
                return $child;
            } else {
                $child = $this->getCurrentItem($child);
                if ($child) {
                    return $child;
                }
            }
        }

        return null;
    }
}
