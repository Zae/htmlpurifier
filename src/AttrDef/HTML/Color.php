<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Exception;

/**
 * Validates a color according to the HTML spec.
 */
class Color extends AttrDef
{
    /**
     * @param string                $string
     * @param \HTMLPurifier\Config  $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     * @throws Exception
     */
    public function validate(string $string, ?\HTMLPurifier\Config $config, ?\HTMLPurifier\Context $context)
    {
        static $colors = null;
        if ($colors === null) {
            $colors = $config->get('Core.ColorKeywords');
        }

        $string = trim($string);

        if (empty($string)) {
            return false;
        }
        $lower = strtolower($string);
        if (isset($colors[$lower])) {
            return $colors[$lower];
        }

        if ($string[0] === '#') {
            $hex = substr($string, 1);
        } else {
            $hex = $string;
        }

        $length = \strlen($hex);
        if ($length !== 3 && $length !== 6) {
            return false;
        }

        if (!ctype_xdigit($hex)) {
            return false;
        }

        if ($length === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return "#$hex";
    }
}
