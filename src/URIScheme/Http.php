<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;

/**
 * Validates http (HyperText Transfer Protocol) as defined by RFC 2616
 */
class Http extends URIScheme
{
    /**
     * @var int
     */
    public $default_port = 80;

    /**
     * @var bool
     */
    public $browsable = true;

    /**
     * @var bool
     */
    public $hierarchical = true;

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

        return true;
    }
}
