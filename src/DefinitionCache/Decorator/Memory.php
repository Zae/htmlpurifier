<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache\Decorator;

use HTMLPurifier\Config;
use HTMLPurifier\DefinitionCache\Decorator;
use HTMLPurifier\Definition;
use HTMLPurifier\Exception;

/**
 * Definition cache decorator class that saves all cache retrievals
 * to PHP's memory; good for unit tests or circumstances where
 * there are lots of configuration objects floating around.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Memory extends Decorator
{
    /**
     * @var array|null
     */
    protected $definitions;

    /**
     * @var string
     */
    public $name = 'Memory';

    /**
     * Memory constructor.
     */
    final public function __construct()
    {
        // just here to finalize the constructor.
    }

    /**
     * @return Memory
     */
    public function copy(): Decorator
    {
        return new static();
    }

    /**
     * @param Definition          $def
     * @param Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function add(Definition $def, Config $config)
    {
        $status = parent::add($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function set(Definition $def, Config $config)
    {
        $status = parent::set($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function replace(Definition $def, Config $config)
    {
        $status = parent::replace($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function get(Config $config)
    {
        $key = $this->generateKey($config);
        if (isset($this->definitions[$key])) {
            return $this->definitions[$key];
        }

        $this->definitions[$key] = parent::get($config);

        return $this->definitions[$key];
    }
}
