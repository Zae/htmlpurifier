<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class Https extends Http
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
