<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates a boolean attribute
 */
class Boolean extends AttrDef
{
    /**
     * @type string|bool
     */
    protected $name;

    /**
     * @type bool
     */
    public $minimized = true;

    /**
     * @param bool|string $name
     */
    public function __construct($name = false)
    {
        $this->name = $name;
    }

    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        return $this->name;
    }

    /**
     * @param string $string Name of attribute
     *
     * @return Boolean
     */
    public function make(string $string): \HTMLPurifier\AttrDef
    {
        return new Boolean($string);
    }
}
