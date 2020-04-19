<?php

declare(strict_types=1);

/**
 * @file
 * Defines a function wrapper for HTML Purifier for quick use.
 * @note ''HTMLPurifier()'' is NOT the same as ''new HTMLPurifier()''
 */

use HTMLPurifier\Exception;

/**
 * Purify HTML.
 *
 * @param string $html   String HTML to purify
 * @param mixed  $config Configuration to use, can be any value accepted by
 *                       \HTMLPurifier\Config::create()
 *
 * @return string
 * @throws Exception
 */
function HTMLPurifier($html, $config = null): string
{
    static $purifier = false;
    if (!$purifier) {
        $purifier = new HTMLPurifier();
    }
    return $purifier->purify($html, $config);
}
