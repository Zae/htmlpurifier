<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\URI;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;

/**
 * Validates an IPv4 address
 *
 * @author Feyd @ forums.devnetwork.net (public domain)
 */
class IPv4 extends AttrDef
{
    /**
     * IPv4 regex, protected so that IPv6 can reuse it.
     *
     * @var string
     */
    protected $ip4;

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?Config $config, ?Context $context)
    {
        if (!$this->ip4) {
            $this->loadRegex();
        }

        if (preg_match('#^' . $this->ip4 . '$#s', $string)) {
            return $string;
        }

        return false;
    }

    /**
     * Lazy load function to prevent regex from being stuffed in
     * cache.
     */
    protected function loadRegex(): void
    {
        $oct = '(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]|[0-9])'; // 0-255
        $this->ip4 = "(?:{$oct}\\.{$oct}\\.{$oct}\\.{$oct})";
    }
}
