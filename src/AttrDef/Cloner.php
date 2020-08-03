<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
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
     * @var AttrDef
     */
    protected $clone;

    /**
     * @param AttrDef $clone
     */
    public function __construct(AttrDef $clone)
    {
        $this->clone = $clone;
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string|null
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        return $this->clone->validate($string, $config, $context);
    }

    /**
     * @param string $string
     *
     * @return AttrDef
     */
    public function make(string $string): AttrDef
    {
        return clone $this->clone;
    }
}
