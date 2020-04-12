<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier\Context;
use HTMLPurifier_Exception;

/**
 * Validates the border property as defined by CSS.
 */
class Border extends AttrDef
{
    /**
     * Local copy of properties this property is shorthand for.
     *
     * @type AttrDef[]
     */
    protected $info = [];

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();

        $this->info['border-width'] = $def->info['border-width'];
        $this->info['border-style'] = $def->info['border-style'];
        $this->info['border-top-color'] = $def->info['border-top-color'];
    }

    /**
     * @param string                $string
     * @param HTMLPurifier_Config   $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parseCDATA($string);
        $string = $this->mungeRgb($string);

        $bits = explode(' ', $string);
        $done = []; // segments we've finished
        $ret = ''; // return value

        foreach ($bits as $bit) {
            foreach ($this->info as $propname => $validator) {
                if (isset($done[$propname])) {
                    continue;
                }

                $r = $validator->validate($bit, $config, $context);
                if ($r !== false) {
                    $ret .= $r . ' ';
                    $done[$propname] = true;
                    break;
                }
            }
        }

        return rtrim($ret);
    }
}
