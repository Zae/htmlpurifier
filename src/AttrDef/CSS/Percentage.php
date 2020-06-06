<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

use function strlen;

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
    public function __construct(bool $non_negative = false)
    {
        $this->number_def = new Number($non_negative);
    }

    /**
     * @param string               $string
     * @param Config $config
     * @param Context              $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
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
