<?php
/**
 * Menu cache warmer
 *
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  CacheWarmer
 */

namespace Millwright\MenuBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Millwright\MenuBundle\Menu\MenuBuilderInterface;

/**
 * @author      Stefan Zerkalica <zerkalica@gmail.com>
 * @category    Millwright
 * @package     MenuBundle
 * @subpackage  CacheWarmer
 */
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
        $this->menuBuilder->loadCache($cacheDir);
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
