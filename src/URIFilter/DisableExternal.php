<?php

declare(strict_types=1);

namespace HTMLPurifier\URIFilter;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\URIDefinition;
use HTMLPurifier\URIFilter;
use HTMLPurifier\URI;
use HTMLPurifier\Exception;

use function is_null;

/**
 * Class HTMLPurifier\URIFilter\HTMLPurifier_URIFilter_DisableExternal
 */
class DisableExternal extends URIFilter
{
    /**
     * @var string
     */
    public $name = 'DisableExternal';

    /**
     * @var array|boolean
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
        /** @var URIDefinition|null $def */
        $def = $config->getDefinition('URI');

        if (is_null($def)) {
            return false;
        }

        $our_host = $def->host;

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
    public function filter(URI &$uri, Config $config, Context $context): bool
    {
        if (is_null($uri->host)) {
            return true;
        }

        if ($this->ourHostParts === false) {
            return false;
        }

        $host_parts = array_reverse(explode('.', $uri->host));
        if (\is_array($this->ourHostParts)) {
            foreach ($this->ourHostParts as $i => $x) {
                if (!isset($host_parts[$i])) {
                    return false;
                }

                if ($host_parts[$i] !== $this->ourHostParts[$i]) {
                    return false;
                }
            }
        }

        return true;
    }
}
