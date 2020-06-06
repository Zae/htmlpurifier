<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;

/**
 * Validates the value for the CSS property text-decoration
 *
 * @note This class could be generalized into a version that acts sort of
 *       like Enum except you can compound the allowed values.
 */
class TextDecoration extends AttrDef
{
    /**
     * @param string                $string
     * @param \HTMLPurifier\Config  $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?\HTMLPurifier\Config $config, ?\HTMLPurifier\Context $context)
    {
        static $allowed_values = [
            'line-through' => true,
            'overline' => true,
            'underline' => true,
        ];

        $string = strtolower($this->parseCDATA($string));

        if ($string === 'none') {
            return $string;
        }

        $parts = explode(' ', $string);
        $final = '';
        foreach ($parts as $part) {
            if (isset($allowed_values[$part])) {
                $final .= $part . ' ';
            }
        }

        $final = rtrim($final);
        if ($final === '') {
            return false;
        }

        return $final;
    }
}
