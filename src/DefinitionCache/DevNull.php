<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache;

use HTMLPurifier\Definition;
use \HTMLPurifier\Config;
use HTMLPurifier\DefinitionCache;

/**
 * Null cache object to use when no caching is on.
 */
class DevNull extends DefinitionCache
{
    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function add(Definition $def, \HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function set(Definition $def, \HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function replace(Definition $def, \HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function remove(\HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function get(\HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function flush(\HTMLPurifier\Config $config): bool
    {
        return false;
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     */
    public function cleanup(\HTMLPurifier\Config $config): bool
    {
        return false;
    }
}
