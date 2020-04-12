<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;

/**
 * Class HTMLPurifier_URIFilter_DisableExternal
 */
class HTMLPurifier_URIFilter_DisableExternal extends URIFilter
{
    /**
     * @type string
     */
    public $name = 'DisableExternal';

    /**
     * @type array
     */
    protected $ourHostParts = false;

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return void
     * @throws HTMLPurifier_Exception
     */
    public function prepare(HTMLPurifier_Config $config): void
    {
        $our_host = $config->getDefinition('URI')->host;

        if ($our_host !== null) {
            $this->ourHostParts = array_reverse(explode('.', $our_host));
        }
    }

    /**
     * @param URI                 $uri Reference
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, HTMLPurifier_Config $config, Context $context)
    {
        if (is_null($uri->host)) {
            return true;
        }

        if ($this->ourHostParts === false) {
            return false;
        }

        $host_parts = array_reverse(explode('.', $uri->host));
        foreach ($this->ourHostParts as $i => $x) {
            if (!isset($host_parts[$i])) {
                return false;
            }

            if ($host_parts[$i] !== $this->ourHostParts[$i]) {
                return false;
            }
        }

        return true;
    }
}
