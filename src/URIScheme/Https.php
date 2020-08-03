<?php

declare(strict_types=1);

namespace HTMLPurifier\URIScheme;

/**
 * Validates https (Secure HTTP) according to http scheme.
 */
class Https extends Http
{
    /**
     * @var int
     */
    public $default_port = 443;

    /**
     * @var bool
     */
    public $secure = true;
}
