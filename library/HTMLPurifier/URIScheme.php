<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\URI;

/**
 * Validator for the components of a URI for a specific scheme
 */
abstract class HTMLPurifier_URIScheme
{
    /**
     * Scheme's default port (integer). If an explicit port number is
     * specified that coincides with the default port, it will be
     * elided.
     *
     * @type int
     */
    public $default_port = null;

    /**
     * Whether or not URIs of this scheme are locatable by a browser
     * http and ftp are accessible, while mailto and news are not.
     *
     * @type bool
     */
    public $browsable = false;

    /**
     * Whether or not data transmitted over this scheme is encrypted.
     * https is secure, http is not.
     *
     * @type bool
     */
    public $secure = false;

    /**
     * Whether or not the URI always uses <hier_part>, resolves edge cases
     * with making relative URIs absolute
     *
     * @type bool
     */
    public $hierarchical = false;

    /**
     * Whether or not the URI may omit a hostname when the scheme is
     * explicitly specified, ala file:///path/to/file. As of writing,
     * 'file' is the only scheme that browsers support his properly.
     *
     * @type bool
     */
    public $may_omit_host = false;

    /**
     * Validates the components of a URI for a specific scheme.
     *
     * @param URI                 $uri Reference to a HTMLPurifier\HTMLPurifier_URI object
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool success or failure
     */
    abstract public function doValidate(URI &$uri, HTMLPurifier_Config $config, Context $context);

    /**
     * Public interface for validating components of a URI.  Performs a
     * bunch of default actions. Don't overload this method.
     *
     * @param URI                 $uri Reference to a HTMLPurifier\HTMLPurifier_URI object
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool success or failure
     * @throws HTMLPurifier_Exception
     */
    public function validate(URI &$uri, HTMLPurifier_Config $config, Context $context)
    {
        if ($this->default_port === $uri->port) {
            $uri->port = null;
        }

        // kludge: browsers do funny things when the scheme but not the
        // authority is set
        if ((!$this->may_omit_host &&
             // if the scheme is present, a missing host is always in error
             (!is_null($uri->scheme) && ($uri->host === '' || is_null($uri->host)))) ||
            // if the scheme is not present, a *blank* host is in error,
            // since this translates into '///path' which most browsers
            // interpret as being 'http://path'.
            ($uri->host === '' && is_null($uri->scheme))
        ) {
            do {
                if (is_null($uri->scheme) && substr($uri->path, 0, 2) !== '//') {
                    $uri->host = null;
                    break;
                }
                // first see if we can manually insert a hostname
                $host = $config->get('URI.Host');
                if (!is_null($host)) {
                    $uri->host = $host;
                } else {
                    // we can't do anything sensible, reject the URL.
                    return false;
                }
            } while (false);
        }

        return $this->doValidate($uri, $config, $context);
    }
}
