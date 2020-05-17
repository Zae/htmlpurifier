<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates a MultiLength as defined by the HTML spec.
 *
 * A multilength is either a integer (pixel count), a percentage, or
 * a relative number.
 */
class MultiLength extends Length
{
    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if ($string === '') {
            return false;
        }

        $parent_result = parent::validate($string, $config, $context);
        if ($parent_result !== false) {
            return $parent_result;
        }

        $length = \strlen($string);
        $last_char = $string[$length - 1];

        if ($last_char !== '*') {
            return false;
        }

        $int = substr($string, 0, $length - 1);

        if ($int === '') {
            return '*';
        }

        if (!is_numeric($int)) {
            return false;
        }

        $int = (int)$int;
        if ($int < 0) {
            return false;
        }

        if ($int === 0) {
            return '0';
        }

        if ($int === 1) {
            return '*';
        }

        return ((string)$int) . '*';
    }
}
