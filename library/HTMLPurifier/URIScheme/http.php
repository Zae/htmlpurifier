<?php

declare(strict_types=1);

/**
 * Validates http (HyperText Transfer Protocol) as defined by RFC 2616
 */
class HTMLPurifier_URIScheme_http extends HTMLPurifier_URIScheme
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
     * @param HTMLPurifier_URI     $uri
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return bool
     */
    public function doValidate(HTMLPurifier_URI &$uri, HTMLPurifier_Config $config, HTMLPurifier_Context $context): bool
    {
        $uri->userinfo = null;

        return true;
    }
}
