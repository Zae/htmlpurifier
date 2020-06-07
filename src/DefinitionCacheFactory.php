<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\DefinitionCache\Serializer;
use HTMLPurifier\DefinitionCache\Decorator;
use HTMLPurifier\DefinitionCache\DevNull;

use function is_string;

/**
 * Responsible for creating definition caches.
 */
class DefinitionCacheFactory
{
    /**
     * @type array
     */
    protected $caches = ['Serializer' => []];

    /**
     * @type array
     */
    protected $implementations = [];

    /**
     * @type \HTMLPurifier\DefinitionCache\Decorator[]
     */
    protected $decorators = [];

    /**
     * Initialize default decorators
     */
    public function setup(): void
    {
        $this->addDecorator('Cleanup');
    }

    /**
     * Retrieves an instance of global definition cache factory.
     *
     * @param DefinitionCacheFactory|true $prototype
     *
     * @return DefinitionCacheFactory
     */
    public static function instance($prototype = null): DefinitionCacheFactory
    {
        static $instance;
        if ($prototype !== null && $prototype !== true) {
            $instance = $prototype;
        } elseif ($instance === null || $prototype === true) {
            $instance = new static();
            $instance->setup();
        }

        return $instance;
    }

    /**
     * Registers a new definition cache object
     *
     * @param string $short Short name of cache object, for reference
     * @param string $long  Full class name of cache object, for construction
     */
    public function register(string $short, string $long): void
    {
        $this->implementations[$short] = $long;
    }

    /**
     * Factory method that creates a cache object based on configuration
     *
     * @param string $type   Name of definitions handled by cache
     * @param Config $config Config instance
     *
     * @return mixed
     * @throws Exception
     */
    public function create(string $type, Config $config)
    {
        $method = $config->get('Cache.DefinitionImpl');
        if ($method === null) {
            return new DevNull($type);
        }

        if (!empty($this->caches[$method][$type])) {
            return $this->caches[$method][$type];
        }

        if (
            isset($this->implementations[$method]) &&
            class_exists($class = $this->implementations[$method], false)
        ) {
            $cache = new $class($type);
        } else {
            if ($method !== 'Serializer') {
                trigger_error("Unrecognized DefinitionCache $method, using Serializer instead", E_USER_WARNING);
            }

            $cache = new Serializer($type);
        }

        foreach ($this->decorators as $decorator) {
            $new_cache = $decorator->decorate($cache);
            // prevent infinite recursion in PHP 4
            unset($cache);
            $cache = $new_cache;
        }

        $this->caches[$method][$type] = $cache;

        return $this->caches[$method][$type];
    }

    /**
     * Registers a decorator to add to all new cache objects
     *
     * @param Decorator|string $decorator An instance or the name of a decorator
     */
    public function addDecorator($decorator): void
    {
        if (is_string($decorator)) {
            $class = "HTMLPurifier\\DefinitionCache\\Decorator\\$decorator";
            $decorator = new $class();
        }

        $this->decorators[$decorator->name] = $decorator;
    }
}
