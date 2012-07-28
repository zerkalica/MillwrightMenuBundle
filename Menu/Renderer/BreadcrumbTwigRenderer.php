<?php
namespace Millwright\MenuBundle\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Millwright menu twig renderer
 */
class BreadcrumbTwigRenderer extends TwigRenderer
{
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
        $defaultOptions = array_merge(array(
            'block' => 'breadcrumb'
        ), $defaultOptions);
        parent::__construct($environment, $template, $matcher, $defaultOptions);
    }

    /**
     * {@inheritDoc}
     */
    protected function getData(ItemInterface $item)
    {
        $currentItem = $this->getCurrentItem($item->getRoot());
        if (!$currentItem) {
            throw new \ErrorException(strtr('Current item not found for %root_item_name%', array('%root_item_name%' => $item->getRoot()->getName())));
        }
        $data  = $currentItem->getBreadcrumbsArray();
        if(isset($data[0]) && empty($data[0]['uri'])) {
            array_shift($data);
        }

        return $data;
    }

    /**
     * Get current item
     *
     * @param ItemInterface $item
     *
     * @return ItemInterface|null
     */
    private function getCurrentItem(ItemInterface $item)
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
