<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\URIParser;

/**
 * Class UriTestCase
 *
 * @package HTMLPurifier\Tests\Unit
 */
abstract class UriTestCase extends TestCase
{
    /**
     * Prepares two URIs into object form
     * @param string &$uri Reference to string input URI
     * @param string|bool &$expect_uri Reference to string expectation URI
     * @note If $expect_uri is false, it will stay false
     */
    protected function prepareURI(string &$uri, &$expect_uri): void
    {
        $parser = new URIParser();

        if ($expect_uri === true) {
            $expect_uri = $uri;
        }

        $uri = $parser->parse($uri);
        if ($expect_uri !== false) {
            $expect_uri = $parser->parse($expect_uri);
        }
    }

    /**
     * Generates a URI object from the corresponding string
     *
     * @param $uri
     *
     * @return bool|\HTMLPurifier\URI
     */
    protected function createURI(string $uri)
    {
        $parser = new URIParser();
        return $parser->parse($uri);
    }
}
