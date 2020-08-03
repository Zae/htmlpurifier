<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates a number as defined by the CSS spec.
 */
class Number extends AttrDef
{
    /**
     * Indicates whether or not only positive values are allowed.
     *
     * @var bool
     */
    protected $non_negative = false;

    /**
     * @param bool $non_negative indicates whether negatives are forbidden
     */
    public function __construct(bool $non_negative = false)
    {
        $this->non_negative = $non_negative;
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return string|false
     * @warning Some contexts do not pass $config, $context. These
     *          variables should not be used without checking HTMLPurifier\HTMLPurifier_Length
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        $string = $this->parseCDATA($string);

        if ($string === '') {
            return false;
        }

        if ($string === '0') {
            return '0';
        }

        $sign = '';
        switch ($string[0]) {
            case '-':
                if ($this->non_negative) {
                    return false;
                }
                $sign = '-';
                // falls through to +
            case '+':
                $string = substr($string, 1);
        }

        if (ctype_digit($string)) {
            $string = ltrim($string, '0');

            return $string ? $sign . $string : '0';
        }

        // Period is the only non-numeric character allowed
        if (strpos($string, '.') === false) {
            return false;
        }

        [$left, $right] = explode('.', $string, 2);

        if ($left === '' && $right === '') {
            return false;
        }

        if ($left !== '' && !ctype_digit($left)) {
            return false;
        }

        // Remove leading zeros until positive number or a zero stays left
        if (ltrim($left, '0') !== '') {
            $left = ltrim($left, '0');
        } else {
            $left = '0';
        }

        $right = rtrim($right, '0');

        if ($right === '') {
            return $left ? $sign . $left : '0';
        }

        if (!ctype_digit($right)) {
            return false;
        }

        return $sign . $left . '.' . $right;
    }
}
