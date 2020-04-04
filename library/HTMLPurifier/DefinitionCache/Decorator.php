<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_DefinitionCache_Decorator
 */
class HTMLPurifier_DefinitionCache_Decorator extends HTMLPurifier_DefinitionCache
{
    /**
     * Cache object we are decorating
     *
     * @type HTMLPurifier_DefinitionCache
     */
    public $cache;

    /**
     * The name of the decorator
     *
     * @var string
     */
    public $name;

    public function __construct(){}

    /**
     * Lazy decorator function
     *
     * @param HTMLPurifier_DefinitionCache $cache Reference to cache object to decorate
     *
     * @return HTMLPurifier_DefinitionCache_Decorator
     */
    public function decorate(HTMLPurifier_DefinitionCache $cache): HTMLPurifier_DefinitionCache_Decorator
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
     * @return HTMLPurifier_DefinitionCache_Decorator
     */
    public function copy(): HTMLPurifier_DefinitionCache_Decorator
    {
        return new HTMLPurifier_DefinitionCache_Decorator();
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function add(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
    {
        return $this->cache->add($def, $config);
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function set(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
    {
        return $this->cache->set($def, $config);
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function replace(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
    {
        return $this->cache->replace($def, $config);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function get(HTMLPurifier_Config $config)
    {
        return $this->cache->get($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function remove($config)
    {
        return $this->cache->remove($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function flush($config)
    {
        return $this->cache->flush($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function cleanup($config)
    {
        return $this->cache->cleanup($config);
    }
}
