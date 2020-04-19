<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;
use HTMLPurifier_Config;

/**
 * Validates nntp (Network News Transfer Protocol) as defined by generic RFC 1738
 */
class nntp extends URIScheme
{
    /**
     * @type int
     */
    public $default_port = 119;

    /**
     * @type bool
     */
    public $browsable = false;

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function doValidate(URI &$uri, HTMLPurifier_Config $config, Context $context): bool
    {
        $uri->userinfo = null;
        $uri->query = null;

        return true;
    }
}
