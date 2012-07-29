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
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Matcher\Voter\UriVoter;

use Millwright\ConfigurationBundle\Builder\OptionManagerInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuBuilder implements MenuBuilderInterface
{
    protected $factory;
    protected $container;
    protected $currentUri;
    protected $matcher;
    protected $optionManager;
    protected $optionNamespace;

    public function __construct(
        MenuFactoryInterface   $factory,
        MatcherInterface       $matcher,
        ContainerInterface     $container,
        OptionManagerInterface $optionManager,
        $optionNamespace
    ) {
        $this->factory         = $factory;
        $this->container       = $container;
        $this->matcher         = $matcher;
        $this->optionManager   = $optionManager;
        $this->optionNamespace = $optionNamespace;
    }

    /**
     * Get options from option manager
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->optionManager->getOptions($this->optionNamespace);
    }

    /**
     * Get static part of menu item options
     *
     * @param  string $name menu container name
     * @return array
     */
    protected function getMenuOptions($name)
    {
        $options = $this->getOptions();

        return $options['tree'][$name];
    }

    /**
     * Get menu item options
     *
     * @param  string $name menu item name
     * @return array
     */
    protected function getLinkOptions($name)
    {
        $options = $this->getOptions();

        return $options['items'][$name];
    }

    /**
     * Create and setup menu item creation factory
     *
     * @param  array $defaultRouteParams
     * @param  array|[] $routeParams
     * @param  array $extra
     * @return MenuFactoryInterface
     */
    protected function createFactory(array $defaultRouteParams, array $routeParams = array(), array $extra = array())
    {
        //@todo How to pass route params ?
        //1. remove factory from service and create new instance here ?
        //2. clone factory and replace route params (faster?)
        //3. add parameters to options array
        //4. remove per item routeParams, use only per menu defaultRouteParams
        $factory = clone $this->factory;

        $this->addVoter();

        $factory
            ->setDefaultRouteParameters($defaultRouteParams)
            ->setRouteParameters($routeParams)
            ->setDefaultExtraParameters($extra)
        ;

        return $factory;
    }

    protected function addVoter()
    {
        if (!$this->currentUri) {
            $currentUri = $this->container->get('request')->getRequestUri();

            // We remove URI params (after '?') for building correct breadcrumbs and menu
            $pos = strpos($currentUri, '?');
            if($pos !== false) {
                $currentUri = substr($currentUri, 0, $pos);
            }
            $this->currentUri = $currentUri;
            $this->matcher->addVoter(new UriVoter($currentUri));
        };
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createMenu()
     */
    public function createMenu($name,
        array $defaultRouteParams = array(),
        array $routeParams = array(),
        array $extra = array()
    ) {
        $options = $this->getMenuOptions($name);

        return $this->createMenuFromOptions($options, $defaultRouteParams, $extra);
    }

    /**
     * {@inheritDoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createMenuFromOptions()
     */
    public function createMenuFromOptions(
        array $options,
        array $defaultRouteParams = array(),
        array $extra = array()
    ) {
        $routeParams = array();

        $factory = $this->createFactory($defaultRouteParams, $routeParams, $extra);

        return $factory->createFromArray($options);
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createLink()
     */
    public function createLink($name, array $defaultRouteParams = array(), array $extra = array())
    {
        $options = $this->getLinkOptions($name);
        $factory = $this->createFactory($defaultRouteParams, $extra);

        return $factory->createItem($name, $options);
    }
}
