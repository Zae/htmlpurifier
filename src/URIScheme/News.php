<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;
use HTMLPurifier\Config;

/**
 * Validates news (Usenet) as defined by generic RFC 1738
 */
class News extends URIScheme
{
    /**
     * @type bool
     */
    public $browsable = false;

    /**
     * @type bool
     */
    public $may_omit_host = true;

    /**
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function doValidate(URI &$uri, Config $config, Context $context): bool
    {
        $uri->userinfo = null;
        $uri->host = null;
        $uri->port = null;
        $uri->query = null;

        // typecode check needed on path
        return true;
    }
}
