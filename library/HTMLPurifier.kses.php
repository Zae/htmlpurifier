<?php
declare(strict_types=1);

/**
 * @file
 * Emulation layer for code that used kses(), substituting in HTML Purifier.
 */

require_once __DIR__ . '/HTMLPurifier.auto.php';

/**
 * @param      $string
 * @param      $allowed_html
 * @param null $allowed_protocols
 *
 * @return string|null
 * @throws HTMLPurifier_Exception
 */
function kses($string, $allowed_html, $allowed_protocols = null): ?string
{
    $config = HTMLPurifier_Config::createDefault();
    $allowed_elements = [];
    $allowed_attributes = [];

    foreach ($allowed_html as $element => $attributes) {
        $allowed_elements[$element] = true;
        foreach ($attributes as $attribute => $x) {
            $allowed_attributes["$element.$attribute"] = true;
        }
    }

    $config->set('HTML.AllowedElements', $allowed_elements);
    $config->set('HTML.AllowedAttributes', $allowed_attributes);

    if ($allowed_protocols !== null) {
        $config->set('URI.AllowedSchemes', $allowed_protocols);
    }

    $purifier = new HTMLPurifier($config);
    return $purifier->purify($string);
}
