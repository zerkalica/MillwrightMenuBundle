<?php

namespace Millwright\MenuBundle\Config;

interface OptionMergerInterface
{
    /**
     * Normalize all menus options
     *
     * @param  array $options 'tree' and 'parameters' menu hierarchy
     *
     * @return array merged and normalized result
     */
    public function normalize(array $menuOptions);
}
