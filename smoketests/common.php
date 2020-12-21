<?php

declare(strict_types=1);

use HTMLPurifier\Encoder;

header('Content-type: text/html; charset=UTF-8');

require_once __DIR__  . '/../vendor/autoload.php';

error_reporting(E_ALL);

/**
 * @param string $string
 * @return string
 */
function escapeHTML(string $string): string
{
    $string = Encoder::cleanUTF8($string);
    $string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');

    return $string;
}
