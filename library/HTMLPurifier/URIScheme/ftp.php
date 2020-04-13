<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\URIScheme;
use HTMLPurifier\URI;

/**
 * Validates ftp (File Transfer Protocol) URIs as defined by generic RFC 1738.
 */
class HTMLPurifier_URIScheme_ftp extends URIScheme
{
    /**
     * @type int
     */
    public $default_port = 21;

    /**
     * @type bool
     */
    public $browsable = true; // usually

    /**
     * @type bool
     */
    public $hierarchical = true;

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function doValidate(URI &$uri, HTMLPurifier_Config $config, Context $context): bool
    {
        $uri->query = null;

        // typecode check
        $semicolon_pos = strrpos($uri->path, ';'); // reverse
        if ($semicolon_pos !== false) {
            $type = substr($uri->path, $semicolon_pos + 1); // no semicolon
            $uri->path = substr($uri->path, 0, $semicolon_pos);
            $type_ret = '';
            if (strpos($type, '=') !== false) {
                // figure out whether or not the declaration is correct
                [$key, $typecode] = explode('=', $type, 2);
                if ($key !== 'type') {
                    // invalid key, tack it back on encoded
                    $uri->path .= '%3B' . $type;
                } elseif ($typecode === 'a' || $typecode === 'i' || $typecode === 'd') {
                    $type_ret = ";type=$typecode";
                }
            } else {
                $uri->path .= '%3B' . $type;
            }

            $uri->path = str_replace(';', '%3B', $uri->path);
            $uri->path .= $type_ret;
        }

        return true;
    }
}
