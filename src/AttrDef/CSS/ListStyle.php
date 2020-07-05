<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

use function is_null;

/**
 * Validates shorthand CSS property list-style.
 *
 * @warning Does not support url tokens that have internal spaces.
 */
class ListStyle extends AttrDef
{
    /**
     * Local copy of validators.
     *
     * @var array<AttrDef|AttrDef\Switcher>
     * @note See HTMLPurifier_AttrDef_CSS_Font::$info for a similar impl.
     */
    protected $info = [];

    /**
     * @param Config $config
     *
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        $def = $config->getCSSDefinition();

        if (is_null($def)) {
            throw new Exception('CSSDefinition not found');
        }

        $this->info['list-style-type'] = $def->info['list-style-type'];
        $this->info['list-style-position'] = $def->info['list-style-position'];
        $this->info['list-style-image'] = $def->info['list-style-image'];
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        // regular pre-processing
        $string = $this->parseCDATA($string);
        if ($string === '') {
            return false;
        }

        // assumes URI doesn't have spaces in it
        $bits = explode(' ', strtolower($string)); // bits to process

        $caught = [];
        $caught['type'] = false;
        $caught['position'] = false;
        $caught['image'] = false;

        $i = 0; // number of catches
        $none = false;

        foreach ($bits as $bit) {
            if ($i >= 3) {
                return false;
            } // optimization bit

            if ($bit === '') {
                continue;
            }

            foreach ($caught as $key => $status) {
                if ($status !== false) {
                    continue;
                }

                $r = $this->info['list-style-' . $key]->validate($bit, $config, $context);
                if ($r === false) {
                    continue;
                }

                if ($r === 'none') {
                    if ($none) {
                        continue;
                    }

                    $none = true;
                    if ($key === 'image') {
                        continue;
                    }
                }

                $caught[$key] = $r;
                $i++;
                break;
            }
        }

        if (!$i) {
            return false;
        }

        $ret = [];

        // construct type
        /**
         * @psalm-suppress TypeDoesNotContainType
         */
        if ($caught['type']) {
            $ret[] = $caught['type'];
        }

        // construct image
        /**
         * @psalm-suppress TypeDoesNotContainType
         */
        if ($caught['image']) {
            $ret[] = $caught['image'];
        }

        // construct position
        /**
         * @psalm-suppress TypeDoesNotContainType
         */
        if ($caught['position']) {
            $ret[] = $caught['position'];
        }

        if (empty($ret)) {
            return false;
        }

        return implode(' ', $ret);
    }
}
