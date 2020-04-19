<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Implements safety checks for safe iframes.
 *
 * @warning This filter is *critical* for ensuring that %HTML.SafeIframe
 * works safely.
 */
class SafeIframe extends URIFilter
{
    /**
     * @type string
     */
    public $name = 'SafeIframe';

    /**
     * @type bool
     */
    public $always_load = true;

    /**
     * @type string
     */
    protected $regexp = null;

    // XXX: The not so good bit about how this is all set up now is we
    // can't check HTML.SafeIframe in the 'prepare' step: we have to
    // defer till the actual filtering.
    /**
     * @param \HTMLPurifier\Config $config
     *
     * @return bool
     * @throws \HTMLPurifier\Exception
     */
    public function prepare(\HTMLPurifier\Config $config): bool
    {
        $this->regexp = $config->get('URI.SafeIframeRegexp');

        return true;
    }

    /**
     * @param URI                 $uri
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return bool|int
     * @throws \HTMLPurifier\Exception
     */
    public function filter(URI &$uri, \HTMLPurifier\Config $config, Context $context)
    {
        // check if filter not applicable
        if (!$config->get('HTML.SafeIframe')) {
            return true;
        }

        // check if the filter should actually trigger
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }

        $token = $context->get('CurrentToken', true);
        if (!($token && $token->name === 'iframe')) {
            return true;
        }

        // check if we actually have some whitelists enabled
        if ($this->regexp === null) {
            return false;
        }

        // actually check the whitelists
        return preg_match($this->regexp, $uri->toString());
    }
}
