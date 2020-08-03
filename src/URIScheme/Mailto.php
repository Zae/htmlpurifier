<?php

declare(strict_types=1);

// VERY RELAXED! Shouldn't cause problems, not even Firefox checks if the
// email is valid, but be careful!
namespace HTMLPurifier\URIScheme;

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;
use HTMLPurifier\Config;

/**
 * Validates mailto (for E-mail) according to RFC 2368
 *
 * @todo Validate the email address
 * @todo Filter allowed query parameters
 */
class Mailto extends URIScheme
{
    /**
     * @var bool
     */
    public $browsable = false;

    /**
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
        $uri->userinfo = null;
        $uri->host = null;
        $uri->port = null;

        // we need to validate path against RFC 2368's addr-spec
        return true;
    }
}
