<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use \HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

/**
 * Validates shorthand CSS property background.
 *
 * @warning Does not support url tokens that have internal spaces.
 */
class Background extends AttrDef
{
    /**
     * Local copy of component validators.
     *
     * @type AttrDef[]
     * @note See HTMLPurifier_AttrDef_Font::$info for a similar impl.
     */
    protected $info;

    /**
     * @param \HTMLPurifier\Config $config
     *
     * @throws \HTMLPurifier\Exception
     */
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();

        $this->info['background-color'] = $def->info['background-color'];
        $this->info['background-image'] = $def->info['background-image'];
        $this->info['background-repeat'] = $def->info['background-repeat'];
        $this->info['background-attachment'] = $def->info['background-attachment'];
        $this->info['background-position'] = $def->info['background-position'];
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
        // regular pre-processing
        $string = $this->parseCDATA($string);
        if ($string === '') {
            return false;
        }

        // munge rgb() decl if necessary
        $string = $this->mungeRgb($string);

        // assumes URI doesn't have spaces in it
        $bits = explode(' ', $string); // bits to process

        $caught = [];
        $caught['color'] = false;
        $caught['image'] = false;
        $caught['repeat'] = false;
        $caught['attachment'] = false;
        $caught['position'] = false;

        $i = 0; // number of catches

        foreach ($bits as $bit) {
            if ($bit === '') {
                continue;
            }
            foreach ($caught as $key => $status) {
                if ($key !== 'position') {
                    if ($status !== false) {
                        continue;
                    }
                    $r = $this->info['background-' . $key]->validate($bit, $config, $context);
                } else {
                    $r = $bit;
                }

                if ($r === false) {
                    continue;
                }

                if ($key === 'position') {
                    if ($caught[$key] === false) {
                        $caught[$key] = '';
                    }
                    $caught[$key] .= $r . ' ';
                } else {
                    $caught[$key] = $r;
                }

                $i++;
                break;
            }
        }

        if (!$i) {
            return false;
        }

        /**
         * @psalm-suppress TypeDoesNotContainType
         */
        if ($caught['position'] !== false) {
            $caught['position'] = $this->info['background-position']->validate($caught['position'], $config, $context);
        }

        $ret = [];
        foreach ($caught as $value) {
            if ($value === false) {
                continue;
            }
            $ret[] = $value;
        }

        if (empty($ret)) {
            return false;
        }

        return implode(' ', $ret);
    }
}
