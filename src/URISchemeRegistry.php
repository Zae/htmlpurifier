<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier_Config;
use HTMLPurifier_Exception;

/**
 * Registry for retrieving specific URI scheme validator objects.
 */
class URISchemeRegistry
{
    /**
     * Retrieve sole instance of the registry.
     *
     * @param URISchemeRegistry $prototype              Optional prototype to overload sole instance with,
     *                                                  or bool true to reset to default registry.
     *
     * @return URISchemeRegistry
     * @note Pass a registry object $prototype with a compatible interface and
     *       the function will copy it and return it all further times.
     */
    public static function instance(?URISchemeRegistry $prototype = null)
    {
        static $instance = null;

        if ($prototype !== null) {
            $instance = $prototype;
        } elseif ($instance === null || $prototype === true) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Cache of retrieved schemes.
     *
     * @type URIScheme[]
     */
    protected $schemes = [];

    /**
     * Retrieves a scheme validator object
     *
     * @param string              $scheme String scheme name like http or mailto
     * @param HTMLPurifier_Config $config
     *
     * @return URIScheme|null
     * @throws HTMLPurifier_Exception
     */
    public function getScheme(?string $scheme, HTMLPurifier_Config $config)
    {
        if (!$config) {
            $config = HTMLPurifier_Config::createDefault();
        }

        // important, otherwise attacker could include arbitrary file
        $allowed_schemes = $config->get('URI.AllowedSchemes');
        if (!isset($allowed_schemes[$scheme]) &&
            !$config->get('URI.OverrideAllowedSchemes')
        ) {
            return null;
        }

        if (isset($this->schemes[$scheme])) {
            return $this->schemes[$scheme];
        }
        if (!isset($allowed_schemes[$scheme])) {
            return null;
        }

        $class = 'HTMLPurifier\\URIScheme\\' . $scheme;
        if (!class_exists($class)) {
            return null;
        }

        $this->schemes[$scheme] = new $class();

        return $this->schemes[$scheme];
    }

    /**
     * Registers a custom scheme to the cache, bypassing reflection.
     *
     * @param string    $scheme Scheme name
     * @param URIScheme $scheme_obj
     */
    public function register(string $scheme, URIScheme $scheme_obj)
    {
        $this->schemes[$scheme] = $scheme_obj;
    }
}
