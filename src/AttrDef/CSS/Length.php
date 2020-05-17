<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;

/**
 * Represents a Length as defined by CSS.
 */
class Length extends AttrDef
{
    /**
     * @type \HTMLPurifier\Length|string
     */
    protected $min;

    /**
     * @type \HTMLPurifier\Length|string
     */
    protected $max;

    /**
     * @param \HTMLPurifier\Length|string $min Minimum length, or null for no bound. String is also acceptable.
     * @param \HTMLPurifier\Length|string $max Maximum length, or null for no bound. String is also acceptable.
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min !== null ? \HTMLPurifier\Length::make($min) : null;
        $this->max = $max !== null ? \HTMLPurifier\Length::make($max) : null;
    }

    /**
     * @param string                $string
     * @param \HTMLPurifier\Config   $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);

        // Optimizations
        if ($string === '') {
            return false;
        }

        if ($string === '0') {
            return '0';
        }

        if (\strlen($string) === 1) {
            return false;
        }

        $length = \HTMLPurifier\Length::make($string);
        if (!$length->isValid()) {
            return false;
        }

        if ($this->min) {
            $c = $length->compareTo($this->min);
            if ($c === false) {
                return false;
            }
            if ($c < 0) {
                return false;
            }
        }

        if ($this->max) {
            $c = $length->compareTo($this->max);
            if ($c === false) {
                return false;
            }
            if ($c > 0) {
                return false;
            }
        }

        return $length->toString();
    }
}
