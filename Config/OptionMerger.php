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

use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Annotations\Reader;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Millwright\MenuBundle\Annotation\Menu;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  Config
 */
class OptionMerger implements OptionMergerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(
        RouterInterface $router,
        Reader $reader
    )
    {
        $this->router  = $router;
        $this->reader  = $reader;
    }

    protected function getDefaultParams()
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
            'showNonAuthorized'   => false,
            'showAsText'          => false,
            'type'                => null,
        );
    }

    /**
     * Get action method by route name
     *
     * @param  string $name route name
     * @return \ReflectionMethod
     */
    private function getActionMethod($name)
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
    private function mergeAnnotations(array $options, array $annotations,
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
    private function annotationToArray($annotation)
    {
        $options = array();
        foreach((array) $annotation as $key => $value) {
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
     * @param  array $options
     * @param  array $parameters
     * @param  string $name
     * @return void
     */
    private function merge(array & $options, array & $parameters, $name)
    {
        $options += array(
            'children' => array(),
            'secureParams' => array('class' => null)
        );

        if (empty($options['roles'])) {
            //MenuNodeDefinition uses beforeNormalization, roles always exists
            unset($options['roles']);
        }

        if($name) {
            $classAnnotations = array();
            $arguments        = array();

            if (isset($parameters[$name])) {
                $options += $parameters[$name];
            }

            if (empty($options['uri'])) {
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
            }

            $options = $this->mergeAnnotations($options, $classAnnotations, $arguments);

            $options += $this->getDefaultParams();

            $parameters[$name] = $options;
            unset($parameters[$name]['children']);
        }

        foreach($options['children'] as $name => & $child) {
            $this->merge($child, $parameters, $name);
        }
    }

    /**
     * {@inheritdoc}
     * @see Millwright\MenuBundle\Config.OptionMergerInterface::normalize()
     */
    public function normalize(array $menuOptions)
    {
        foreach($menuOptions['tree'] as $name => & $menu) {
            $this->merge($menu, $menuOptions['items'], $name);
            $menuOptions['items'][$name] = null;
        }

        return $menuOptions;
    }
}
