<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\CSS\Number;
use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Validates a Percentage as defined by the CSS spec.
 */
class Percentage extends AttrDef
{
    /**
     * Instance to defer number validation to.
     *
     * @type Number
     */
    protected $number_def;

    /**
     * @param bool $non_negative Whether to forbid negative values
     */
    public function __construct($non_negative = false)
    {
        $this->number_def = new Number($non_negative);
    }

    /**
     * @param string               $string
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);

        if ($string === '') {
            return false;
        }

        $length = strlen($string);
        if ($length === 1) {
            return false;
        }

        if ($string[$length - 1] !== '%') {
            return false;
        }

        $number = substr($string, 0, $length - 1);
        $number = $this->number_def->validate($number, $config, $context);

        if ($number === false) {
            return false;
        }

        return "$number%";
    }
}
