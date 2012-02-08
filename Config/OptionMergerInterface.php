<?php

namespace Millwright\MenuBundle\Config;

interface OptionMergerInterface
{
    /**
     * This params will be copied to MenuItem object if not set in config
     *
     * @return array [string]
     */
    public function getDefaultParams();

    /**
     * Copy some values from $parentOptions to $options,
     *
     * @param  array $options destination
     * @param  array $parentOptions source
     *
     * @return array result
     */
    public function merge(array $options, array $parentOptions = array());
}
