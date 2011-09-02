<?php
/**
 * @author   Stefan Zerkalica <zerkalica@gmail.com>
 * @category Millwright
 */
namespace Millwright\MenuBundle;

use Knp\Bundle\MenuBundle\MenuItem as KnpMenuItem;
use InvalidArgumentException;
use Millwright\MenuBundle\MenuContext;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 */
class MenuItem extends KnpMenuItem
{
    /**
     * Access roles for menu item
     *
     * @var string
     */
    protected $roles = array();

    /**
     * Route name for uri generation
     *
     * @var string
     */
    protected $route;
    /**
     * Params for uri generation from route
     *
     * @var array
     */
    protected $routeParams;

    /**
     * Generate absolute uri from route
     *
     * @var boolean
     */
    protected $absolute;

    /**
     * @var array[string]
     */
    protected $translateParams = array();

    /**
     * Translation domain
     *
     * @var string
     */
    protected $domain;

    /**
     * @var MenuContext
     */
    protected $menuContext;

    /**
     * @var string
     */
    protected $childClass;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, $uri = null, $attributes = array())
    {
        //parent::__construct($name, $uri, $attributes);
    }

    /**
     * Get default params for child menu item creation factory
     *
     * @return array [string]
     */
    protected function getDefaultParams()
    {
        return array(
            'name',
            'label',
            'uri',
            'domain',
            'attributes',
            'roles',
            'translateParams',
            'route',
            'routeParams',
            'absolute',
            //'submenu',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($withChildren = true)
    {
        $array = array();

        $fields = $this->getDefaultParams();
        if ($withChildren) {
            $fields[] = 'submenu';
        }

        foreach ($fields as $propName) {
            $method = 'get' . ucfirst($propName);
            $array[$propName] = $this->$method();
        }

        return $array;
    }

    /**
     * @see KnpMenuItem::fromArray()
     */
    protected function _fromArray(array $array)
    {
        foreach ($this->getDefaultParams() as $key) {
            if (isset($array[$key])) {
                $method = 'set' . ucfirst($key);
                $this->$method($array[$key]);
            }
        }

        $this->end();

        if(isset($array['submenu'])) {
            $this->setSubmenu($array['submenu']);
        }


        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($array)
    {
        return $this->_fromArray($array);
    }

    /**
     * {@inheritdoc}
     */
    protected function createChild($name = null, $uri = null, $attributes = array(), $class = null)
    {
        $class = $this->getChildClass();
        $child = new $class;
        $child->setParent($this);

        if ($name) {
            $child->name = $name;
        }

        if ($uri) {
            $child->setUri($uri);
        }
        if ($attributes) {
            $child->setAttributes($attributes);
        }

        return $child;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(KnpMenuItem $parent = null)
    {
        parent::setParent($parent);
        if ($parent) {
            $this->setShowChildren($parent->getShowChildren());
            $this->setCurrentUri($parent->getCurrentUri());
            $this->setNum($parent->count());

            $this
                ->setRoles($parent->getRoles())
                ->setRouteParams($parent->getRouteParams())
                ->setMenuContext($parent->getMenuContext())
                ->setChildClass($parent->getChildClass())
                ->setDomain($parent->getDomain())
            ;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($child, $uri = null, $attributes = array(), $class = null)
    {
        if (!$child instanceof MenuItem) {
            $child = $this->createChild($child, $uri, $attributes, $class);
        }
        else{
            if ($child->getParent()) {
                throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
            } else {
                $child->setParent($this);
            }
        }

        $this->children[$child->getName()] = $child;

        return $child;
    }

    /**
     * @return \Millwright\MenuBundle\MenuItem
     */
    public function end()
    {
        $this->menuContext->setupMenuItem($this);

        return $this;
    }

    /**
     * Set menu
     *
     * @param  MenuContext $menuContext
     * @return MenuItem
     */
    public function setMenuContext(MenuContext $menuContext)
    {
        $this->menuContext = $menuContext;

        return $this;
    }

    public function getMenuContext()
    {
        return $this->menuContext;
    }

    /**
     * Set children from array for current menu item
     *
     * @param  array $array
     * @return \Millwright\MenuBundle\MenuItem
     */
    protected function setSubmenu(array $array)
    {
        foreach ($array as $name => $child) {
            $childItem = $this->addChild($name, null, array(),
                isset($child['class']) ? $child['class'] : $this->getChildClass()
            );

            $childItem->_fromArray($child);
        }

        return $this;
    }

    /**
     * Get childs array
     *
     * @return array
     */
    protected function getSubmenu()
    {
        $array = array();
        foreach ($this->getChildren() as $child) {
            $array[$child->getName()] = $child->toArray();
        }

        return $array();
    }

    /**
     * Set child class
     *
     * @param  string $childClass
     * @return MenuItem
     */
    public function setChildClass($childClass)
    {
        $this->childClass = $childClass;

        return $this;
    }

    /**
     * Get child class for child menu item creation factory
     *
     * @return  string
     */
    public function getChildClass()
    {
        if (!$this->childClass) {
            $this->childClass = get_class($this);
        }

        return $this->childClass;
    }

    /**
     * Set roles for menu item
     *
     * @param  array $roles
     * @return MenuItem
     */
    public function setRoles(array $roles)
    {
        $this->roles = (array) $roles;

        return $this;
    }

    /**
     * Get roles for menu item
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set translation params
     *
     * @param  array $params
     * @return \Millwright\MenuBundle\MenuItem
     *
     */
    public function setTranslateParams(array $params)
    {
        $this->translateParams = $params;

        return $this;
    }

    public function getTranslateParams()
    {
        return $this->translateParams;
    }

    /**
     * Set translation domain
     *
     * @param  string $domain
     * @return \Millwright\MenuBundle\MenuItem
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set route
     *
     * @param  string $route
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route ? $this->route : $this->name;
    }

    /**
     * Set routeParams
     *
     * @param  array $routeParams
     * @return MenuItem
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = (array) $routeParams;

        return $this;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Set absolute
     *
     * @param  boolean $absolute
     * @return MenuItem
     */
    public function setAbsolute($absolute)
    {
        $this->absolute = $absolute;

        return $this;
    }

    public function getAbsolute()
    {
        return $this->absolute;
    }
}
