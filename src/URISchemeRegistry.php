<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * Registry for retrieving specific URI scheme validator objects.
 */
class URISchemeRegistry
{
    /**
     * Retrieve sole instance of the registry.
     *
     * @param URISchemeRegistry|true|null $prototype    Optional prototype to overload sole instance with,
     *                                                  or bool true to reset to default registry.
     *
     * @return URISchemeRegistry
     * @note Pass a registry object $prototype with a compatible interface and
     *       the function will copy it and return it all further times.
     */
    public static function instance($prototype = null): URISchemeRegistry
    {
        static $instance = null;

        if ($prototype !== null && $prototype !== true) {
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
     * @param string       $scheme String scheme name like http or mailto
     * @param Config|null  $config
     *
     * @param Context|null $context
     *
     * @return URIScheme|null
     * @throws Exception
     */
    public function getScheme(?string $scheme, ?Config $config, ?Context $context): ?URIScheme
    {
        if (\is_null($scheme)) {
            return null;
        }

        if (\is_null($config)) {
            $config = Config::createDefault();
        }

        // important, otherwise attacker could include arbitrary file
        $allowed_schemes = $config->get('URI.AllowedSchemes');
        if (!isset($allowed_schemes[$scheme]) && !$config->get('URI.OverrideAllowedSchemes')) {
            return null;
        }

        if (isset($this->schemes[$scheme])) {
            return $this->schemes[$scheme];
        }

        if (!isset($allowed_schemes[$scheme])) {
            return null;
        }

        $class = 'HTMLPurifier\\URIScheme\\' . ucfirst($scheme);
        if (!class_exists($class)) {
            return null;
        }

        /** @var URIScheme $object */
        $object = new $class();
        $this->schemes[$scheme] = $object;

        return $this->schemes[$scheme];
    }

    /**
     * Registers a custom scheme to the cache, bypassing reflection.
     *
     * @param string    $scheme Scheme name
     * @param URIScheme $scheme_obj
     */
    public function register(string $scheme, URIScheme $scheme_obj): void
    {
        $this->schemes[$scheme] = $scheme_obj;
    }
}
