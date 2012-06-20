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
use Millwright\MenuBundle\Config\OptionMergerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Matcher\Voter\UriVoter;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Menu
 */
class MenuBuilder implements MenuBuilderInterface
{
    /**
     * @var MenuFactoryInterface
     */
    protected $factory;

    /**
     * @var OptionMergerInterface
     */
    protected $merger;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $menuOptions;

    /**
     * @var array
     */
    protected $compiledOptions;

    /**
     *
     * @var ContainerInterface
     */
    protected  $container;

    protected $currentUri;
    protected $matcher;

    public function __construct(
        MenuFactoryInterface  $factory,
        OptionMergerInterface $merger,
        MatcherInterface      $matcher,
        ContainerInterface    $container,
        array                 $options,
        array                 $menuOptions
    ) {
        $this->factory     = $factory;
        $this->merger      = $merger;
        $this->options     = $options;
        $this->menuOptions = $menuOptions;
        $this->container   = $container;
        $this->matcher     = $matcher;
    }

    public function loadCache($cacheDir = null)
    {
        if(null === $this->compiledOptions) {

            if(!$cacheDir) {
                $cacheDir = $this->options['cache_dir'];
            }

            $class = $this->options['generator_cache_class'];
            $cache = $cacheDir
                ? new ConfigCache($cacheDir . '/'
                    . $class . '.php',
                    $this->options['debug'])
                : null
            ;

            if(!$cache || !$cache->isFresh()) {
                $this->compiledOptions = $this->merger->normalize($this->menuOptions);
                $cache->write('<?php return ' . var_export($this->compiledOptions, true) . ';');
            } else {
                $this->compiledOptions = require_once $cache;
            }
        }
    }

    /**
     * Get static part of menu item options
     *
     * @param  string $name menu container name
     * @return array
     */
    protected function getMenuOptions($name)
    {
        $this->loadCache();

        return $this->compiledOptions['tree'][$name];
    }

    /**
     * Get menu item options
     *
     * @param  string $name menu item name
     * @return array
     */
    protected function getLinkOptions($name)
    {
        $this->loadCache();

        return $this->compiledOptions['items'][$name];
    }

    /**
     * Create and setup menu item creation factory
     *
     * @param  array $defaultRouteParams
     * @param  array|[] $routeParams
     * @return MenuFactoryInterface
     */
    protected function createFactory(array $defaultRouteParams, array $routeParams = array())
    {
        //@todo How to pass route params ?
        //1. remove factory from service and create new instance here ?
        //2. clone factory and replace route params (faster?)
        //3. add parameters to options array
        //4. remove per item routeParams, use only per menu defaultRouteParams
        $factory = clone $this->factory;

        $this->addVoter();

        $factory
            ->setDefaultRouteParams($defaultRouteParams)
            ->setRouteParams($routeParams)
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
        array $routeParams = array()
    )
    {
        $options = $this->getMenuOptions($name);
        $factory = $this->createFactory($defaultRouteParams, $routeParams);

        return $factory->createFromArray($options);
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Menu.MenuBuilderInterface::createLink()
     */
    public function createLink($name, array $defaultRouteParams = array())
    {
        $options = $this->getLinkOptions($name);
        $factory = $this->createFactory($defaultRouteParams);

        return $factory->createItem($name, $options);
    }
}
