<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\URI\Email;

use HTMLPurifier\AttrDef\URI\Email;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Primitive email validation class based on the regexp found at
 * http://www.regular-expressions.info/email.html
 */
class SimpleCheck extends Email
{
    /**
     * @param string  $string
     * @param Config  $config
     * @param Context $context
     *
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        // no support for named mailboxes i.e. "Bob <bob@example.com>"
        // that needs more percent encoding to be done
        if ($string === '') {
            return false;
        }

        $string = trim($string);
        $result = preg_match('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $string);

        return $result ? $string : false;
    }
}
