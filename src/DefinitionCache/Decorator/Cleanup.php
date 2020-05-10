<?php

declare(strict_types=1);

namespace HTMLPurifier\DefinitionCache\Decorator;

use HTMLPurifier\DefinitionCache\Decorator;
use HTMLPurifier\Definition;
use HTMLPurifier\Config;

/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Cleanup extends Decorator
{
    /**
     * @var string
     */
    public $name = 'Cleanup';

    /**
     * @return Cleanup
     */
    public function copy(): Decorator
    {
        return new static();
    }

    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return mixed
     */
    public function add(Definition $def, Config $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return mixed
     */
    public function set(Definition $def, Config $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param Definition $def
     * @param Config     $config
     *
     * @return mixed
     */
    public function replace(Definition $def, Config $config)
    {
        $status = parent::replace($def, $config);
        if (!$status) {
            $this->cleanup($config);
        }

        return $status;
    }

    /**
     * @param Config $config
     *
     * @return mixed
     */
    public function get(Config $config)
    {
        $ret = parent::get($config);
        if (!$ret) {
            $this->cleanup($config);
        }

        return $ret;
    }
}
