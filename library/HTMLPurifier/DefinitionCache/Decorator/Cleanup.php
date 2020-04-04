<?php

declare(strict_types=1);

/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 */
class HTMLPurifier_DefinitionCache_Decorator_Cleanup extends HTMLPurifier_DefinitionCache_Decorator
{
    /**
     * @type string
     */
    public $name = 'Cleanup';

    /**
     * @return HTMLPurifier_DefinitionCache_Decorator_Cleanup
     */
    public function copy(): HTMLPurifier_DefinitionCache_Decorator
    {
        return new static();
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function add(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function set(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config     $config
     *
     * @return mixed
     */
    public function replace(HTMLPurifier_Definition $def, HTMLPurifier_Config $config)
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
