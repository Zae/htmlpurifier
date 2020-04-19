<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;
use \HTMLPurifier\Config;

/**
 * Validates http (HyperText Transfer Protocol) as defined by RFC 2616
 */
class http extends URIScheme
{
    /**
     * @type int
     */
    public $default_port = 80;

    /**
     * @type bool
     */
    public $browsable = true;

    /**
     * @type bool
     */
    public $hierarchical = true;

    /**
     * @param URI                 $uri
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function doValidate(URI &$uri, \HTMLPurifier\Config $config, Context $context): bool
    {
        $uri->userinfo = null;

        return true;
    }
}
