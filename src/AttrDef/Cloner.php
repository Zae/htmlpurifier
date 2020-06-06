<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Context;

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
     * @param string               $string
     * @param \HTMLPurifier\Config $config
     * @param Context              $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?\HTMLPurifier\Config $config, ?\HTMLPurifier\Context $context)
    {
        return $this->clone->validate($string, $config, $context);
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
