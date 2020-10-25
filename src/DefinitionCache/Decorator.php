<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache;

use HTMLPurifier\Config;
use HTMLPurifier\Definition;
use HTMLPurifier\DefinitionCache;

/**
 * Class HTMLPurifier\ChildDef\HTMLPurifier_DefinitionCache_Decorator
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Decorator extends DefinitionCache
{
    /**
     * Cache object we are decorating
     *
     * @var DefinitionCache
     */
    public $cache;

    /**
     * The name of the decorator
     *
     * @var string
     */
    public $name;

    /**
     * Decorator constructor.
     */
    public function __construct()
    {
        // empty by choice.
    }

    /**
     * Lazy decorator function
     *
     * @param DefinitionCache $cache Reference to cache object to decorate
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
     * @param Config $config
     *
     * @return mixed
     */
    public function add(Definition $def, Config $config)
    {
        return $this->cache->add($def, $config);
    }

    /**
     * @param Definition          $def
     * @param Config $config
     *
     * @return mixed
     */
    public function set(Definition $def, Config $config)
    {
        return $this->cache->set($def, $config);
    }

    /**
     * @param Definition          $def
     * @param Config $config
     *
     * @return mixed
     */
    public function replace(Definition $def, Config $config)
    {
        return $this->cache->replace($def, $config);
    }

    /**
     * @param Config $config
     *
     * @return mixed
     */
    public function get(Config $config)
    {
        return $this->cache->get($config);
    }

    /**
     * @param Config $config
     *
     * @return bool
     */
    public function remove($config): bool
    {
        return $this->cache->remove($config);
    }

    /**
     * @param Config $config
     *
     * @return bool
     */
    public function flush($config): bool
    {
        return $this->cache->flush($config);
    }

    /**
     * @param Config $config
     *
     * @return bool
     */
    public function cleanup($config): bool
    {
        return $this->cache->cleanup($config);
    }

    /**
     * @param string $decorator
     * @return self
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    public static function make(string $decorator): self
    {
        $class = sprintf(
            "%s\\%s",
            __CLASS__,
            $decorator
        );

        return new $class();
    }
}
