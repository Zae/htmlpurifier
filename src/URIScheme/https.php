<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

use HTMLPurifier\URIScheme\http;

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class https extends http
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
