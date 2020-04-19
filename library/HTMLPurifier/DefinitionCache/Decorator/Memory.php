<?php

declare(strict_types=1);

use HTMLPurifier\DefinitionCache\Decorator;
use HTMLPurifier\Definition;
use HTMLPurifier\Exception;

/**
 * Definition cache decorator class that saves all cache retrievals
 * to PHP's memory; good for unit tests or circumstances where
 * there are lots of configuration objects floating around.
 */
class HTMLPurifier_DefinitionCache_Decorator_Memory extends Decorator
{
    /**
     * @type array
     */
    protected $definitions;

    /**
     * @type string
     */
    public $name = 'Memory';

    /**
     * @return HTMLPurifier_DefinitionCache_Decorator_Memory
     */
    public function copy(): Decorator
    {
        return new static();
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function add(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::add($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function set(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::set($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function replace(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::replace($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }

        return $status;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     * @throws Exception
     */
    public function get(HTMLPurifier_Config $config)
    {
        $key = $this->generateKey($config);
        if (isset($this->definitions[$key])) {
            return $this->definitions[$key];
        }

        $this->definitions[$key] = parent::get($config);

        return $this->definitions[$key];
    }
}
