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
     * @var string|bool
     */
    protected $name;

    /**
     * @var bool
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
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
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
     * @return \HTMLPurifier\AttrDef\HTML\Boolean
     */
    public function make(string $string): AttrDef
    {
        return new Boolean($string);
    }
}
