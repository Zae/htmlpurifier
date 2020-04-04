<?php

declare(strict_types=1);

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class HTMLPurifier_URIScheme_https extends HTMLPurifier_URIScheme_http
{
    /**
     * @type int
     */
    public $default_port = 443;
    /**
     * @type bool
     */
    public $secure = true;
}
