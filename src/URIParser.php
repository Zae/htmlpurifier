<?php

declare(strict_types=1);

namespace HTMLPurifier;

use HTMLPurifier\PercentEncoder;

/**
 * Parses a URI into the components and fragment identifier as specified
 * by RFC 3986.
 */
class URIParser
{
    /**
     * Instance of HTMLPurifier\HTMLPurifier_PercentEncoder to do normalization with.
     */
    protected $percentEncoder;

    public function __construct()
    {
        $this->percentEncoder = new PercentEncoder();
    }

    /**
     * Parses a URI.
     *
     * @param string $uri URI to parse
     *
     * @return URI representation of URI. This representation has
     *         not been validated yet and may not conform to RFC.
     */
    public function parse(string $uri): ?URI
    {
        $uri = $this->percentEncoder->normalize($uri);

        // Regexp is as per Appendix B.
        // Note that ["<>] are an addition to the RFC's recommended
        // characters, because they represent external delimeters.
        $r_URI = '!' .
                 '(([a-zA-Z0-9\.\+\-]+):)?' . // 2. Scheme
                 '(//([^/?#"<>]*))?' . // 4. Authority
                 '([^?#"<>]*)' .       // 5. Path
                 '(\?([^#"<>]*))?' .   // 7. Query
                 '(#([^"<>]*))?' .     // 8. Fragment
                 '!';

        $matches = [];
        $result = preg_match($r_URI, $uri, $matches);

        if (!$result) {
            return null;
        } // *really* invalid URI

        // seperate out parts
        $scheme = !empty($matches[1]) ? $matches[2] : null;
        $authority = !empty($matches[3]) ? $matches[4] : null;
        $path = $matches[5]; // always present, can be empty
        $query = !empty($matches[6]) ? $matches[7] : null;
        $fragment = !empty($matches[8]) ? $matches[9] : null;

        // further parse authority
        if ($authority !== null) {
            $r_authority = "/^((.+?)@)?(\[[^\]]+\]|[^:]*)(:(\d*))?/";
            $matches = [];
            preg_match($r_authority, $authority, $matches);
            $userinfo = !empty($matches[1]) ? $matches[2] : null;
            $host = !empty($matches[3]) ? $matches[3] : '';
            $port = !empty($matches[4]) ? (int)$matches[5] : null;
        } else {
            $port = $host = $userinfo = null;
        }

        return new URI($scheme, $userinfo, $host, $port, $path, $query, $fragment);
    }
}
