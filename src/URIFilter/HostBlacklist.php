<?php

declare(strict_types=1);

// It's not clear to me whether or not Punycode means that hostnames
// do not have canonical forms anymore. As far as I can tell, it's
// not a problem (punycoding should be identity when no Unicode
// points are involved), but I'm not 100% sure
namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;

/**
 * Class HostBlacklist
 *
 * @package HTMLPurifier\URIFilter
 */
class HostBlacklist extends URIFilter
{
    /**
     * @var string
     */
    public $name = 'HostBlacklist';

    /**
     * @var array
     */
    protected $blacklist = [];

    /**
     * @param Config $config
     *
     * @return bool
     * @throws Exception
     */
    public function prepare(Config $config): bool
    {
        $this->blacklist = $config->get('URI.HostBlacklist');

        return true;
    }

    /**
     * @param URI     $uri
     * @param Config  $config
     * @param Context $context
     *
     * @return bool
     */
    public function filter(URI &$uri, Config $config, Context $context): bool
    {
        foreach ($this->blacklist as $blacklisted_host_fragment) {
            if (strpos((string)$uri->host, $blacklisted_host_fragment) !== false) {
                return false;
            }
        }

        return true;
    }
}
