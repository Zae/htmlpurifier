<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\URI;

/**
 * Validates an IPv6 address.
 *
 * @author Feyd @ forums.devnetwork.net (public domain)
 * @note   This function requires brackets to have been removed from address
 *       in URI.
 */
class IPv6 extends IPv4
{
    /**
     * @param string                $string
     * @param \HTMLPurifier\Config  $config
     * @param \HTMLPurifier\Context $context
     *
     * @return bool|string
     */
    public function validate(string $string, ?\HTMLPurifier\Config $config, ?\HTMLPurifier\Context $context)
    {
        if (!$this->ip4) {
            $this->loadRegex();
        }

        $original = $string;

        $pre = '(?:/(?:12[0-8]|1[0-1][0-9]|[1-9][0-9]|[0-9]))'; // /0 - /128

        //      prefix check
        if (strpos($string, '/') !== false) {
            if (preg_match('#' . $pre . '$#s', $string, $find)) {
                $string = substr($string, 0, 0 - \strlen($find[0]));
                unset($find);
            } else {
                return false;
            }
        }

        //      IPv4-compatiblity check
        if (preg_match('#(?<=:' . ')' . $this->ip4 . '$#s', $string, $find)) {
            $string = substr($string, 0, 0 - \strlen($find[0]));
            $ip = explode('.', $find[0]);
            /**
             * @psalm-suppress InvalidScalarArgument
             */
            $ip = array_map('dechex', $ip);
            $string .= $ip[0] . $ip[1] . ':' . $ip[2] . $ip[3];
            unset($find, $ip);
        }

        //      compression check
        $string = explode('::', $string);
        $c = \count($string);
        if ($c > 2) {
            return false;
        }

        if ($c === 2) {
            [$first, $second] = $string;
            $first = explode(':', $first);
            $second = explode(':', $second);

            if (\count($first) + \count($second) > 8) {
                return false;
            }

            while (\count($first) < 8) {
                $first[] = '0';
            }

            array_splice($first, 8 - \count($second), 8, $second);
            $string = $first;
            unset($first, $second);
        } else {
            $string = explode(':', $string[0]);
        }
        $c = \count($string);

        if ($c !== 8) {
            return false;
        }

        //      All the pieces should be 16-bit hex strings. Are they?
        foreach ($string as $piece) {
            if (!preg_match('#^[0-9a-fA-F]{4}$#s', sprintf('%04s', $piece))) {
                return false;
            }
        }

        return $original;
    }
}
