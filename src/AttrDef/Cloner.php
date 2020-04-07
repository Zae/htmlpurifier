<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Dummy AttrDef that mimics another AttrDef, BUT it generates clones
 * with make.
 */
class Cloner extends AttrDef
{
    /**
     * What we're cloning.
     *
     * @type AttrDef
     */
    protected $clone;

    /**
     * @param AttrDef $clone
     */
    public function __construct($clone)
    {
        $this->clone = $clone;
    }

    /**
     * @param string               $v
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool|string
     */
    public function validate($v, $config, $context)
    {
        return $this->clone->validate($v, $config, $context);
    }

    /**
     * @param string $string
     *
     * @return AttrDef
     */
    public function make($string)
    {
        return clone $this->clone;
    }
}
