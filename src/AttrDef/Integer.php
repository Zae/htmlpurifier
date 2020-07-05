<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
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
    public function __construct(bool $negative = true, bool $zero = true, bool $positive = true)
    {
        $this->negative = $negative;
        $this->zero = $zero;
        $this->positive = $positive;
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
        $string = $this->parseCDATA($string);
        if ($string === '') {
            return false;
        }

        // we could possibly simply typecast it to integer, but there are
        // certain fringe cases that must not return an integer.

        // clip leading sign
        if ($this->negative && $string[0] === '-') {
            $digits = substr($string, 1);
            if ($digits === '0') {
                $string = '0';
            } // rm minus sign for zero
        } elseif ($this->positive && $string[0] === '+') {
            $digits = $string = substr($string, 1); // rm unnecessary plus
        } else {
            $digits = $string;
        }

        // test if it's numeric
        if (!ctype_digit($digits)) {
            return false;
        }

        // perform scope tests
        if (!$this->zero && (int)$string === 0) {
            return false;
        }

        if (!$this->positive && (int)$string > 0) {
            return false;
        }

        if (!$this->negative && (int)$string < 0) {
            return false;
        }

        return $string;
    }
}
