<?php

declare(strict_types=1);

/**
 * Validates news (Usenet) as defined by generic RFC 1738
 */
class HTMLPurifier_URIScheme_news extends HTMLPurifier_URIScheme
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
     * @param HTMLPurifier_URI     $uri
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool
     */
    public function doValidate(HTMLPurifier_URI &$uri, HTMLPurifier_Config $config, HTMLPurifier_Context $context): bool
    {
        $uri->userinfo = null;
        $uri->host = null;
        $uri->port = null;
        $uri->query = null;

        // typecode check needed on path
        return true;
    }
}
