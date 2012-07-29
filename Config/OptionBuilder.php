<?php
/**
 * Normalize and merge menu options from controller annotations, menu items and tree configs
 *
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Config
 */
namespace Millwright\MenuBundle\Config;

use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Millwright\ConfigurationBundle\Builder\OptionBuilderBase;
use Millwright\ConfigurationBundle\Configuration\ConfigurationHelperInterface;
use Millwright\ConfigurationBundle\Configuration\RouteInfo;

use Millwright\MenuBundle\Annotation\Menu;
use Millwright\MenuBundle\Annotation\MenuDefault;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Config
 */
class OptionBuilder extends OptionBuilderBase
{
    protected $helper;

    public function __construct(ConfigurationHelperInterface $helper) {
        $this->helper = $helper;
    }

    protected function getDefaultParams()
    {
        return array(
            'uri'                     => null,
            'label'                   => null,
            'name'                    => null,
            'attributes'              => array(),
            'linkAttributes'          => array(),
            'childrenAttributes'      => array(),
            'labelAttributes'         => array(),
            'display'                 => true,
            'displayChildren'         => true,
            'secureParams'            => array(),
            'roles'                   => array(),
            'route'                   => null,
            'routeAbsolute'           => false,
            'routeAcceptedParameters' => array(),
            'routeRequiredParameters' => array(),
            'showNonAuthorized'       => false,
            'showAsText'              => false,
            'translateDomain'         => null,
            'translateParameters'     => array(),
            'type'                    => null,
        );
    }

    /**
     * Merge array of annotations to options
     *
     * @param  array $options
     * @param  array $annotations
     * @param        array[\ReflectionParameter] $arguments
     *
     * @return array
     */
    protected function getAnnotations(
        array $annotations,
        array $arguments = array()
    ) {
        $options = array();
        foreach ($annotations as $params) {
            foreach ($params as $param) {
                if ($param instanceof SecureParam) {
                    /** @var $param SecureParam */
                    $options['secureParams'][$param->name] = $this->annotationToArray($param);
                    /* @var $argument \ReflectionParameter */
                    $argument = $arguments[$param->name];
                    $class    = $argument->getClass();
                    if (!$class) {
                        throw new \InvalidArgumentException(sprintf(
                            'Secured action parameter has no class definition'
                        ));
                    }

                    $options['secureParams'][$param->name]['class'] = $class->getName();
                } else {
                    if ($param instanceof Secure || $param instanceof Menu || $param instanceof MenuDefault) {
                        $options += $this->annotationToArray($param);
                    }
                }
            }
        }

        foreach ($annotations as $params) {
            foreach ($params as $param) {
                if ($param instanceof ParamConverter && isset($options['secureParams'][$param->getName()])) {
                    /** @var $param ParamConverter*/
                    $options['secureParams'][$param->getName()]['class'] = $param->getClass();
                }
            }
        }

        return $options;
    }

    /**
     * Convert annotation object to array, remove empty values
     *
     * @param  Object $annotation
     *
     * @return array
     */
    protected function annotationToArray($annotation)
    {
        $options = array();
        foreach ((array) $annotation as $key => $value) {
            if ($value !== null) {
                $options[$key] = $value;
            }
        }

        return $options;
    }

    /**
     * Detects controller::action by route name in options.
     *
     * Loads and merge options from annotations and menu config:
     * 1. From @Menu, @Secure, @SecureParam annotations of method
     * 2. From millwright_menu:items section of config
     * 3. From @Menu, @Secure, @SecureParam annotations of class
     * 4. Normalize empty params from array @see OptionMerger::getDefaultParams()
     *
     * @param  array  $options
     * @param  array  $parameters
     * @param  string $name
     *
     * @return void
     */
    protected function merge(array & $options, array & $parameters, $name)
    {
        foreach ($options as $key => $value) {
            if (empty($value)) {
                unset($options[$key]);
            }
        }

        if (isset($parameters[$name])) {
            $options += $parameters[$name];
        }

        if ($name) {
            $annotationsOptions = array();
            if (empty($options['uri'])) {
                $route     = isset($options['route']) ? $options['route'] : $name;
                $routeInfo = $this->helper->getRouteInfo($route);
                if ($routeInfo) {
                    $methodInfo = $routeInfo->getMethodInfo();

                    $annotationsOptions = $this->getAnnotations($methodInfo->getConfigurations(), $methodInfo->getArguments());

                    $annotationsOptions += array(
                        'route'                   => $route,
                        'routeAcceptedParameters' => array_flip($routeInfo->getAcceptedParameters()),
                        'routeRequiredParameters' => $routeInfo->getRequiredParameters()
                    );
                }
            }

            $options += $annotationsOptions;
            $options += $this->getDefaultParams();

            $parameters[$name] = $options;
            unset($parameters[$name]['children']);
        }

        $options += array(
            'children' => array(),
        );
        foreach ($options['children'] as $name => & $child) {
            $this->merge($child, $parameters, $name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $menuOptions = parent::build();

        foreach ($menuOptions['items'] as & $item) {
            foreach ($item as $key => $value) {
                if (empty($value)) {
                    unset($item[$key]);
                }
            }
        }

        foreach ($menuOptions['tree'] as $name => & $menu) {
            $this->merge($menu, $menuOptions['items'], $name);
            $menuOptions['items'][$name] = null;
        }

        return $menuOptions;
    }
}
