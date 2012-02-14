<?php

namespace Millwright\MenuBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Millwright\MenuBundle\Menu\MenuBuilderInterface;

class MenuCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var MenuBuilderInterface
     */
    protected $menuBuilder;

    public function __construct(MenuBuilderInterface $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        if ($this->menuBuilder instanceof WarmableInterface) {
            $this->menuBuilder->warmUp($cacheDir);
        }
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * @return Boolean always true
     */
    public function isOptional()
    {
        return true;
    }
}
