<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrDef\URI;

use HTMLPurifier\AttrDef;
use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Exception;

use function defined;
use function is_null;
use function strlen;

/**
 * Validates a host according to the IPv4, IPv6 and DNS (future) specifications.
 */
class Host extends AttrDef
{
    /**
     * IPv4 sub-validator.
     *
     * @var IPv4
     */
    protected $ipv4;

    /**
     * IPv6 sub-validator.
     *
     * @var IPv6
     */
    protected $ipv6;

    public function __construct()
    {
        $this->ipv4 = new IPv4();
        $this->ipv6 = new IPv6();
    }

    /**
     * @param string       $string
     * @param Config|null  $config
     * @param Context|null $context
     *
     * @return string|null
     * @throws Exception
     */
    public function validate(string $string, ?Config $config, ?Context $context): ?string
    {
        $length = strlen($string);
        // empty hostname is OK; it's usually semantically equivalent:
        // the default host as defined by a URI scheme is used:
        //
        //      If the URI scheme defines a default for host, then that
        //      default applies when the host subcomponent is undefined
        //      or when the registered name is empty (zero length).
        if ($string === '') {
            return '';
        }

        if ($length > 1 && $string[0] === '[' && $string[$length - 1] === ']') {
            //IPv6
            $ip = substr($string, 1, $length - 2);
            $valid = $this->ipv6->validate($ip, $config, $context);

            if ($valid === false) {
                return null;
            }

            return '[' . $valid . ']';
        }

        // need to do checks on unusual encodings too
        $ipv4 = $this->ipv4->validate($string, $config, $context);
        if ($ipv4 !== false) {
            return $ipv4;
        }

        // A regular domain name.

        // This doesn't match I18N domain names, but we don't have proper IRI support,
        // so force users to insert Punycode.

        // There is not a good sense in which underscores should be
        // allowed, since it's technically not! (And if you go as
        // far to allow everything as specified by the DNS spec...
        // well, that's literally everything, modulo some space limits
        // for the components and the overall name (which, by the way,
        // we are NOT checking!).  So we (arbitrarily) decide this:
        // let's allow underscores wherever we would have allowed
        // hyphens, if they are enabled.  This is a pretty good match
        // for browser behavior, for example, a large number of browsers
        // cannot handle foo_.example.com, but foo_bar.example.com is
        // fairly well supported.
        $underscore = '';
        if (!is_null($config) && $config->get('Core.AllowHostnameUnderscore')) {
            $underscore = '_';
        }

        // Based off of RFC 1738, but amended so that
        // as per RFC 3696, the top label need only not be all numeric.
        // The productions describing this are:
        $a = '[a-z]';     // alpha
        $an = '[a-z0-9]';  // alphanum
        $and = "[a-z0-9-$underscore]"; // alphanum | "-"

        // domainlabel = alphanum | alphanum *( alphanum | "-" ) alphanum
        $domainlabel = "$an(?:$and*$an)?";

        // AMENDED as per RFC 3696
        // toplabel    = alphanum | alphanum *( alphanum | "-" ) alphanum
        //      side condition: not all numeric
        $toplabel = "$an(?:$and*$an)?";

        // hostname    = *( domainlabel "." ) toplabel [ "." ]
        if (preg_match("/^(?:$domainlabel\.)*($toplabel)\.?$/i", $string, $matches)) {
            if (!ctype_digit($matches[1])) {
                return $string;
            }
        }

        if (defined('IDNA_NONTRANSITIONAL_TO_ASCII') && defined('INTL_IDNA_VARIANT_UTS46')) {
            $string = idn_to_ascii($string, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
        } else {
            $string = idn_to_ascii($string);
        }

        // Try again
        if (preg_match("/^($domainlabel\.)*$toplabel\.?$/i", (string)$string)) {
            return $string;
        }

        return null;
    }
}
