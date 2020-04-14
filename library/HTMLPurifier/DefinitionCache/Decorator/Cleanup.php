<?php

declare(strict_types=1);

use HTMLPurifier\DefinitionCache\Decorator;
use HTMLPurifier\Definition;

/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 */
class HTMLPurifier_DefinitionCache_Decorator_Cleanup extends Decorator
{
    /**
     * @type string
     */
    public $name = 'Cleanup';

    /**
     * @return HTMLPurifier_DefinitionCache_Decorator_Cleanup
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
     */
    public function add(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function set(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param Definition          $def
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function replace(Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::replace($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return mixed
     */
    public function get(HTMLPurifier_Config $config)
    {
        $ret = parent::get($config);
        if (!$ret) {
            $this->cleanup($config);
        }

        return $ret;
    }
}
