<?php

namespace Millwright\MenuBundle\Config;

interface OptionMergerInterface
{
    /**
     * Normalize all menus options
     *
     * @param  array $options destination
     *
     * @return array result
     */
    public function normalize(array $options);
}
