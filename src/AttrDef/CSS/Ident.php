<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;

/**
 * Validates based on {ident} CSS grammar production
 */
class Ident extends AttrDef
{
    /**
     * @param string                $string
     * @param \HTMLPurifier\Config   $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = trim($string);

        // early abort: '' and '0' (strings that convert to false) are invalid
        if (!$string) {
            return false;
        }

        $pattern = '/^(-?[A-Za-z_][A-Za-z_\-0-9]*)$/';
        if (!preg_match($pattern, $string)) {
            return false;
        }

        return $string;
    }
}
