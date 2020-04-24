<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use \HTMLPurifier\Config;
use HTMLPurifier\Context;

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
     * @param string  $number
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return string|bool
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
