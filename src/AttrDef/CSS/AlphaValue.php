<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Class HTMLPurifier_AttrDef_CSS_AlphaValue
 */
class AlphaValue extends Number
{
    public function __construct()
    {
        parent::__construct(false); // opacity is non-negative, but we will clamp it
    }

    /**
     * @param string               $number
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return string
     */
    public function validate($number, $config, $context)
    {
        $result = parent::validate($number, $config, $context);
        if ($result === false) {
            return $result;
        }

        $float = (float)$result;
        if ($float < 0.0) {
            $result = '0';
        }

        if ($float > 1.0) {
            $result = '1';
        }

        return $result;
    }
}
