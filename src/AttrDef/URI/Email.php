<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\URI;

use HTMLPurifier\AttrDef;

/**
 * Class HTMLPurifier_AttrDef_URI_Email
 */
abstract class Email extends AttrDef
{
    /**
     * Unpacks a mailbox into its display-name and address
     *
     * @param string $string
     */
    public function unpack($string)
    {
        // needs to be implemented
    }
}

// sub-implementations
