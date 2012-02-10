<?php

namespace Millwright\MenuBundle\Config;

interface OptionMergerInterface
{
    /**
     * Normalize all menus options
     *
     * @param  array $hierarchy menu hierarchy
     * @param  array $parameters plain dictionary of parameters
     *
     * @return array merged and normalized result
     */
    public function normalize(array $hierarchy, array $parameters);
}
