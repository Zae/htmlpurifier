<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache;

use HTMLPurifier\Definition;
use HTMLPurifier_Config;
use HTMLPurifier\DefinitionCache;

/**
 * Null cache object to use when no caching is on.
 */
class DevNull extends DefinitionCache
{
    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function add(Definition $def, HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function set(Definition $def, HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function replace(Definition $def, HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function remove(HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function get(HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function flush(HTMLPurifier_Config $config): bool
    {
        return false;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     */
    public function cleanup(HTMLPurifier_Config $config): bool
    {
        return false;
    }
}
