<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\AttrDef\CSS\AlphaValue;
use \HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

/**
 * Validates Color as defined by CSS.
 */
class Color extends AttrDef
{
    /**
     * @type AlphaValue
     */
    protected $alpha;

    public function __construct()
    {
        $this->alpha = new AlphaValue();
    }

    /**
     * @param string              $color
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool|string
     * @throws Exception
     *
     * @psalm-suppress RedundantCondition (line 143)
     */
    public function validate($color, $config, $context)
    {
        static $colors = null;
        if ($colors === null) {
            $colors = $config->get('Core.ColorKeywords');
        }

        $color = trim($color);
        if ($color === '') {
            return false;
        }

        $lower = strtolower($color);
        if (isset($colors[$lower])) {
            return $colors[$lower];
        }

        if (preg_match('#(rgb|rgba|hsl|hsla)\(#', $color, $matches) === 1) {
            $length = \strlen($color);
            if (strpos($color, ')') !== $length - 1) {
                return false;
            }

            // get used function : rgb, rgba, hsl or hsla
            $function = $matches[1];

            $parameters_size = 3;
            $alpha_channel = false;
            if (substr($function, -1) === 'a') {
                $parameters_size = 4;
                $alpha_channel = true;
            }

            /*
             * Allowed types for values :
             * parameter_position => [type => max_value]
             */
            $allowed_types = [
                1 => ['percentage' => 100, 'integer' => 255],
                2 => ['percentage' => 100, 'integer' => 255],
                3 => ['percentage' => 100, 'integer' => 255],
            ];
            $allow_different_types = false;

            if (strpos($function, 'hsl') !== false) {
                $allowed_types = [
                    1 => ['integer' => 360],
                    2 => ['percentage' => 100],
                    3 => ['percentage' => 100],
                ];
                $allow_different_types = true;
            }

            $values = trim(str_replace($function, '', $color), ' ()');

            $parts = explode(',', $values);
            if (\count($parts) !== $parameters_size) {
                return false;
            }

            $type = false;
            $new_parts = [];
            $i = 0;

            foreach ($parts as $part) {
                $i++;
                $part = trim($part);

                if ($part === '') {
                    return false;
                }

                // different check for alpha channel
                if ($alpha_channel === true && $i === \count($parts)) {
                    $result = $this->alpha->validate($part, $config, $context);

                    if ($result === false) {
                        return false;
                    }

                    $new_parts[] = (string)$result;
                    continue;
                }

                if (substr($part, -1) === '%') {
                    $current_type = 'percentage';
                } else {
                    $current_type = 'integer';
                }

                if (!\array_key_exists($current_type, $allowed_types[$i])) {
                    return false;
                }

                if (!$type) {
                    $type = $current_type;
                }

                if ($allow_different_types === false && $type !== $current_type) {
                    return false;
                }

                $max_value = $allowed_types[$i][$current_type];

                if ($current_type === 'integer') {
                    // Return value between range 0 -> $max_value
                    $new_parts[] = (int)max(min($part, $max_value), 0);
                } elseif ($current_type === 'percentage') {
                    $new_parts[] = (float)max(min(rtrim($part, '%'), $max_value), 0) . '%';
                }
            }

            $new_values = implode(',', $new_parts);

            $color = $function . '(' . $new_values . ')';
        } else {
            // hexadecimal handling
            if ($color[0] === '#') {
                $hex = substr($color, 1);
            } else {
                $hex = $color;
                $color = '#' . $color;
            }

            $length = \strlen($hex);
            if ($length !== 3 && $length !== 6) {
                return false;
            }

            if (!ctype_xdigit($hex)) {
                return false;
            }
        }

        return $color;
    }
}
