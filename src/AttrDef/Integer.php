<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Context;

/**
 * Validates an integer.
 *
 * @note While this class was modeled off the CSS definition, no currently
 *       allowed CSS uses this type.  The properties that do are: widows,
 *       orphans, z-index, counter-increment, counter-reset.  Some of the
 *       HTML attributes, however, find use for a non-negative version of this.
 */
class Integer extends AttrDef
{
    /**
     * Whether or not negative values are allowed.
     *
     * @type bool
     */
    protected $negative = true;

    /**
     * Whether or not zero is allowed.
     *
     * @type bool
     */
    protected $zero = true;

    /**
     * Whether or not positive values are allowed.
     *
     * @type bool
     */
    protected $positive = true;

    /**
     * @param bool $negative indicating whether or not negative values are allowed
     * @param bool $zero     indicating whether or not zero is allowed
     * @param bool $positive indicating whether or not positive values are allowed
     */
    public function __construct($negative = true, $zero = true, $positive = true)
    {
        $this->negative = $negative;
        $this->zero = $zero;
        $this->positive = $positive;
    }

    /**
     * @param string              $integer
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool|string
     */
    public function validate($integer, $config, $context)
    {
        $integer = $this->parseCDATA($integer);
        if ($integer === '') {
            return false;
        }

        // we could possibly simply typecast it to integer, but there are
        // certain fringe cases that must not return an integer.

        // clip leading sign
        if ($this->negative && $integer[0] === '-') {
            $digits = substr($integer, 1);
            if ($digits === '0') {
                $integer = '0';
            } // rm minus sign for zero
        } elseif ($this->positive && $integer[0] === '+') {
            $digits = $integer = substr($integer, 1); // rm unnecessary plus
        } else {
            $digits = $integer;
        }

        // test if it's numeric
        if (!ctype_digit($digits)) {
            return false;
        }

        // perform scope tests
        if (!$this->zero && (int)$integer === 0) {
            return false;
        }

        if (!$this->positive && (int)$integer > 0) {
            return false;
        }

        if (!$this->negative && (int)$integer < 0) {
            return false;
        }

        return $integer;
    }
}

// vim: et sw=4 sts=4
