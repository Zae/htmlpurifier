<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates arbitrary text according to the HTML spec.
 */
class Text extends AttrDef
{
    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        return $this->parseCDATA($string);
    }
}
