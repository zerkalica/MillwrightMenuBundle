<?php
namespace Millwright\MenuBundle\Menu;

class MenuOptions
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function get()
    {
        return $this->options;
    }
}
