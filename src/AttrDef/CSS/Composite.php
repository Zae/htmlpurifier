<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\CSS;

use HTMLPurifier\AttrDef;
use HTMLPurifier_Config;
use HTMLPurifier_Context;

/**
 * Allows multiple validators to attempt to validate attribute.
 *
 * Composite is just what it sounds like: a composite of many validators.
 * This means that multiple HTMLPurifier_AttrDef objects will have a whack
 * at the string.  If one of them passes, that's what is returned.  This is
 * especially useful for CSS values, which often are a choice between
 * an enumerated set of predefined values or a flexible data type.
 */
class Composite extends AttrDef
{
    /**
     * List of objects that may process strings.
     *
     * @type AttrDef[]
     * @todo Make protected
     */
    public $defs;

    /**
     * @param AttrDef[] $defs List of HTMLPurifier_AttrDef objects
     */
    public function __construct($defs)
    {
        $this->defs = $defs;
    }

    /**
     * @param string               $string
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        foreach ($this->defs as $i => $def) {
            $result = $this->defs[$i]->validate($string, $config, $context);

            if ($result !== false) {
                return $result;
            }
        }

        return false;
    }
}
