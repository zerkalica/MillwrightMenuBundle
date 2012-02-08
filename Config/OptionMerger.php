<?php

namespace Millwright\MenuBundle\Config;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Annotations\Reader;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Millwright\MenuBundle\Annotation\Menu;

class OptionMerger implements OptionMergerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $routeDefaults;

    /**
     * @var Reader
     */
    protected $reader;

    public function __construct(
        RouterInterface $router,
        Reader $reader,
        array $routeDefaults = array()
    )
    {
        $this->router = $router;
        $this->reader = $reader;
        $this->routeDefaults = $routeDefaults;
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Config.OptionMergerInterface::getDefaultParams()
     */
    public function getDefaultParams()
    {
        return array(
            'uri'                => null,
            'label'              => null,
            'name'               => null,
            'attributes'         => array(),
            'linkAttributes'     => array(),
            'childrenAttributes' => array(),
            'labelAttributes'    => array(),
            'display'            => true,
            'displayChildren'    => true,
            'translateDomain'    => null,
            'translateParameters' => array(),
            'secureParams'        => array(),
            'roles'               => array(),
            'route'               => null,
            'routeAbsolute'       => false,
        );
    }

    /**
     * This params will be copied from parent to child item
     * if not set in child
     *
     * @return array
     */
    protected function getInheritedParams()
    {
        return array('translateDomain', 'routeParameters', 'roles');
    }

    /**
     * Get action method by route name
     *
     * @param  string $name route name
     * @return \ReflectionMethod
     */
    protected function getActionMethod($name)
    {
        //@todo do not use getRouteCollection - not interface method
        // howto get controller and action name by route name ?
        $route = $this->router->getRouteCollection()->get($name);
        if (!$route) {
            return array();
        }

        $defaults = $route->getDefaults();
        if (!isset($defaults['_controller'])) {
            return array();
        }

        $params = explode('::', $defaults['_controller']);

        $class  = new \ReflectionClass($params[0]);
        $method = $class->getMethod($params[1]);

        return $method;
    }

    /**
     * Merge array of annotations to options
     *
     * @param  array $options
     * @param  array $annotations
     * @param  array[\ReflectionParameter] $arguments
     * @return array
     */
    protected function mergeAnnotations(array $options, array $annotations,
        array $arguments = array())
    {
        $secureParams = array();
        foreach($annotations as $param) {
            if ($param instanceof SecureParam) {
                $secureParams[$param->name] = $this->annotationToArray($param);
                /* @var $argument \ReflectionParameter */
                $argument  = $arguments[$param->name];
                $class     = $argument->getDeclaringClass();

                $secureParams[$param->name]['class'] = $class->getName();

            } else if ($param instanceof Secure || $param instanceof Menu ) {
                $options += $this->annotationToArray($param);
            }
        }
        $options['secureParams'] += $secureParams;

        return $options;
    }

    /**
     * Convert annotation object to array, remove empty values
     *
     * @param  Object $annotation
     * @return array
     */
    protected function annotationToArray($annotation)
    {
        $options = array();
        foreach((array) $annotation as $key => $value) {
            if ($value) {
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
     * 2. From millwright_menu:route section of config
     * 3. From parent options item @see OptionMerger::getInheritedParams()
     * 4. From @Menu, @Secure, @SecureParam annotations of class
     * 5. Normalize empty params from array @see OptionMerger::getDefaultParams()
     *
     * @see Millwright\MenuBundle\Config.OptionMergerInterface::merge()
     */
    public function merge(array $options, array $parentOptions = array())
    {
        $classAnnotations = array();
        $arguments        = array();

        if (empty($options['roles'])) {
            //MenuNodeDefinition uses beforeNormalization, roles always exists
            unset($options['roles']);
        }

        $options += array(
            'name' => null,
            'children' => array(),
            'secureParams' => array('class' => null)
        );
        $name = $options['name'];
        $options += array('route' => $name);

        if ($options['route']) {
            $method = $this->getActionMethod($options['route']);
            if ($method) {
                foreach ($method->getParameters() as $argument) {
                    $arguments[$argument->getName()] = $argument;
                }

                $annotations = $this->reader->getMethodAnnotations($method);
                $options     = $this->mergeAnnotations($options, $annotations, $arguments);

                $class       = $method->getDeclaringClass();
                $classAnnotations = $this->reader->getClassAnnotations($class);
            }
        }

        if (isset($this->routeDefaults[$name])) {
            $options += $this->routeDefaults[$name];
        }

        foreach($this->getInheritedParams() as $param) {
            if(!empty($parentOptions[$param]) && !isset($options[$param])) {
                $options[$param] = $parentOptions[$param];
            }
        }

        $options = $this->mergeAnnotations($options, $classAnnotations, $arguments);

        $options += array('routeParameters' => array());

        $options += $this->getDefaultParams();

        return $options;
    }
}
