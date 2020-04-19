<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache;

use HTMLPurifier\Definition;
use \HTMLPurifier\Config;
use HTMLPurifier\DefinitionCache;

/**
 * Class HTMLPurifier\ChildDef\HTMLPurifier_DefinitionCache_Decorator
 */
class Decorator extends DefinitionCache
{
    /**
     * Cache object we are decorating
     *
     * @type \HTMLPurifier\DefinitionCache
     */
    public $cache;

    /**
     * The name of the decorator
     *
     * @var string
     */
    public $name;

    public function __construct()
    {
    }

    /**
     * Lazy decorator function
     *
     * @param \HTMLPurifier\DefinitionCache $cache Reference to cache object to decorate
     *
     * @return Decorator
     */
    public function decorate(DefinitionCache $cache): Decorator
    {
        $decorator = $this->copy();

        // reference is necessary for mocks in PHP 4
        $decorator->cache =& $cache;
        $decorator->type = $cache->type;

        return $decorator;
    }

    /**
     * Cross-compatible clone substitute
     *
     * @return Decorator
     */
    public function copy(): Decorator
    {
        return new Decorator();
    }

    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function add(Definition $def, \HTMLPurifier\Config $config)
    {
        return $this->cache->add($def, $config);
    }

    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function set(Definition $def, \HTMLPurifier\Config $config)
    {
        return $this->cache->set($def, $config);
    }

    /**
     * @param Definition          $def
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function replace(Definition $def, \HTMLPurifier\Config $config)
    {
        return $this->cache->replace($def, $config);
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function get(\HTMLPurifier\Config $config)
    {
        return $this->cache->get($config);
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function remove($config)
    {
        return $this->cache->remove($config);
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function flush($config)
    {
        return $this->cache->flush($config);
    }

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return mixed
     */
    public function cleanup($config)
    {
        return $this->cache->cleanup($config);
    }
}
