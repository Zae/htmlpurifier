<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\HTML;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier\Context;

/**
 * Validates an integer representation of pixels according to the HTML spec.
 */
class Pixels extends AttrDef
{
    /**
     * @type int
     */
    protected $max;

    /**
     * @param int $max
     */
    public function __construct($max = null)
    {
        $this->max = $max;
    }

    /**
     * @param string              $string
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = trim($string);
        if ($string === '0') {
            return $string;
        }

        if ($string === '') {
            return false;
        }

        $length = strlen($string);
        if (substr($string, $length - 2) === 'px') {
            $string = substr($string, 0, $length - 2);
        }

        if (!is_numeric($string)) {
            return false;
        }

        $int = (int)$string;

        if ($int < 0) {
            return '0';
        }

        // upper-bound value, extremely high values can
        // crash operating systems, see <http://ha.ckers.org/imagecrash.html>
        // WARNING, above link WILL crash you if you're using Windows

        if ($this->max !== null && $int > $this->max) {
            return (string)$this->max;
        }

        return (string)$int;
    }

    /**
     * @param string $string
     *
     * @return AttrDef
     */
    public function make(string $string): AttrDef
    {
        if ($string === '') {
            $max = null;
        } else {
            $max = (int)$string;
        }

        $class = get_class($this);

        return new $class($max);
    }
}
