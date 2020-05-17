<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\Context;

/**
 * Validates the HTML type length (not to be confused with CSS's length).
 *
 * This accepts integer pixels or percentages as lengths for certain
 * HTML attributes.
 */
class Length extends Pixels
{
    /**
     * @param string              $string
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
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

        if ($last_char !== '%') {
            return false;
        }

        $points = substr($string, 0, $length - 1);

        if (!is_numeric($points)) {
            return false;
        }

        $points = (int)$points;

        if ($points < 0) {
            return '0%';
        }

        if ($points > 100) {
            return '100%';
        }

        return ((string)$points) . '%';
    }
}
