<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Validates arbitrary text according to the HTML spec.
 */
class Text extends AttrDef
{
    /**
     * @param string               $string
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->parseCDATA($string);
    }
}
