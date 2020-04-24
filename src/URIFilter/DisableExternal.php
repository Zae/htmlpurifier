<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use \HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Class HTMLPurifier\URIFilter\HTMLPurifier_URIFilter_DisableExternal
 */
class DisableExternal extends URIFilter
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
     * @param Config $config
     *
     * @return bool
     * @throws Exception
     */
    public function prepare(Config $config): bool
    {
        /**
         * @psalm-suppress UndefinedPropertyFetch
         */
        $our_host = $config->getDefinition('URI')->host;

        if ($our_host !== null) {
            $this->ourHostParts = array_reverse(explode('.', $our_host));
        }

        return true;
    }

    /**
     * @param URI     $uri Reference
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function filter(URI &$uri, Config $config, Context $context)
    {
        if (\is_null($uri->host)) {
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
