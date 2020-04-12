<?php

declare(strict_types=1);

// It's not clear to me whether or not Punycode means that hostnames
// do not have canonical forms anymore. As far as I can tell, it's
// not a problem (punycoding should be identity when no Unicode
// points are involved), but I'm not 100% sure
use HTMLPurifier\Context;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;

class HTMLPurifier_URIFilter_HostBlacklist extends URIFilter
{
    /**
     * @type string
     */
    public $name = 'HostBlacklist';

    /**
     * @type array
     */
    protected $blacklist = [];

    /**
     * @param HTMLPurifier_Config $config
     *
     * @return bool
     * @throws HTMLPurifier_Exception
     */
    public function prepare(HTMLPurifier_Config $config): bool
    {
        $this->blacklist = $config->get('URI.HostBlacklist');

        return true;
    }

    /**
     * @param URI                 $uri
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function filter(URI &$uri, HTMLPurifier_Config $config, Context $context): bool
    {
        foreach ($this->blacklist as $blacklisted_host_fragment) {
            if (strpos($uri->host, $blacklisted_host_fragment) !== false) {
                return false;
            }
        }

        return true;
    }
}
