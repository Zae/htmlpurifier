<?php

declare(strict_types=1);

/**
 * Validates nntp (Network News Transfer Protocol) as defined by generic RFC 1738
 */
class HTMLPurifier_URIScheme_nntp extends HTMLPurifier_URIScheme
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
     * @param HTMLPurifier_URI     $uri
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool
     */
    public function doValidate(HTMLPurifier_URI &$uri, HTMLPurifier_Config $config, HTMLPurifier_Context $context): bool
    {
        $uri->userinfo = null;
        $uri->query = null;

        return true;
    }
}
