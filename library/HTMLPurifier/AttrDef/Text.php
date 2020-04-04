<?php

declare(strict_types=1);

/**
 * Validates arbitrary text according to the HTML spec.
 */
class HTMLPurifier_AttrDef_Text extends HTMLPurifier_AttrDef
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
