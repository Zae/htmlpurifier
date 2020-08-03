<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;
use HTMLPurifier\Config;

/**
 * Validates file as defined by RFC 1630 and RFC 1738.
 */
class File extends URIScheme
{
    /**
     * Generally file:// URLs are not accessible from most
     * machines, so placing them as an img src is incorrect.
     *
     * @var bool
     */
    public $browsable = false;

    /**
     * Basically the *only* URI scheme for which this is true, since
     * accessing files on the local machine is very common.  In fact,
     * browsers on some operating systems don't understand the
     * authority, though I hear it is used on Windows to refer to
     * network shares.
     *
     * @var bool
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
        // Authentication method is not supported
        $uri->userinfo = null;
        // file:// makes no provisions for accessing the resource
        $uri->port = null;
        // While it seems to work on Firefox, the querystring has
        // no possible effect and is thus stripped.
        $uri->query = null;

        return true;
    }
}
