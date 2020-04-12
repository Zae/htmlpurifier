<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier\Context;

/**
 * Decorator which enables !important to be used in CSS values.
 */
class ImportantDecorator extends AttrDef
{
    /**
     * @type AttrDef
     */
    public $def;
    /**
     * @type bool
     */
    public $allow;

    /**
     * @param AttrDef $def   Definition to wrap
     * @param bool    $allow Whether or not to allow !important
     */
    public function __construct($def, $allow = false)
    {
        $this->def = $def;
        $this->allow = $allow;
    }

    /**
     * Intercepts and removes !important if necessary
     *
     * @param string              $string
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        // test for ! and important tokens
        $string = trim($string);
        $is_important = false;

        // :TODO: optimization: test directly for !important and ! important
        if (strlen($string) >= 9 && substr($string, -9) === 'important') {
            $temp = rtrim(substr($string, 0, -9));
            // use a temp, because we might want to restore important
            if ($temp !== '' && substr($temp, -1) === '!') {
                $string = rtrim(substr($temp, 0, -1));
                $is_important = true;
            }
        }

        $string = $this->def->validate($string, $config, $context);

        if ($this->allow && $is_important) {
            $string .= ' !important';
        }

        return $string;
    }
}
