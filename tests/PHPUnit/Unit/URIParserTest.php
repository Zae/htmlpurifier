<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\URI;
use HTMLPurifier\URIParser;

/**
 * Class URIParserTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class URIParserTest extends TestCase
{
    /**
     * @param      $uri
     * @param      $scheme
     * @param      $userinfo
     * @param      $host
     * @param      $port
     * @param      $path
     * @param      $query
     * @param      $fragment
     * @param null $config
     * @param null $context
     */
    private function assertParsing(
        $uri,
        $scheme,
        $userinfo,
        $host,
        $port,
        $path,
        $query,
        $fragment,
        $config = null,
        $context = null
    ): void {
        $this->prepareCommon($config, $context);
        $parser = new URIParser();
        $result = $parser->parse($uri, $config, $context);
        $expect = new URI($scheme, $userinfo, $host, $port, $path, $query, $fragment);

        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function testPercentNormalization(): void
    {
        $this->assertParsing(
            '%G',
            null, null, null, null, '%25G', null, null
        );
    }

    /**
     * @test
     */
    public function testRegular(): void
    {
        $this->assertParsing(
            'http://www.example.com/webhp?q=foo#result2',
            'http', null, 'www.example.com', null, '/webhp', 'q=foo', 'result2'
        );
    }

    /**
     * @test
     */
    public function testPortAndUsername(): void
    {
        $this->assertParsing(
            'http://user@authority.part:80/now/the/path?query#fragment',
            'http', 'user', 'authority.part', 80, '/now/the/path', 'query', 'fragment'
        );
    }

    /**
     * @test
     */
    public function testPercentEncoding(): void
    {
        $this->assertParsing(
            'http://en.wikipedia.org/wiki/Clich%C3%A9',
            'http', null, 'en.wikipedia.org', null, '/wiki/Clich%C3%A9', null, null
        );
    }

    /**
     * @test
     */
    public function testEmptyQuery(): void
    {
        $this->assertParsing(
            'http://www.example.com/?#',
            'http', null, 'www.example.com', null, '/', '', null
        );
    }

    /**
     * @test
     */
    public function testEmptyPath(): void
    {
        $this->assertParsing(
            'http://www.example.com',
            'http', null, 'www.example.com', null, '', null, null
        );
    }

    /**
     * @test
     */
    public function testOpaqueURI(): void
    {
        $this->assertParsing(
            'mailto:bob@example.com',
            'mailto', null, null, null, 'bob@example.com', null, null
        );
    }

    /**
     * @test
     */
    public function testTelURI(): void
    {
        $this->assertParsing(
            'tel:+1 (555) 555-5555',
            'tel', null, null, null, '+1 (555) 555-5555', null, null
        );
    }

    /**
     * @test
     */
    public function testIPv4Address(): void
    {
        $this->assertParsing(
            'http://192.0.34.166/',
            'http', null, '192.0.34.166', null, '/', null, null
        );
    }

    /**
     * @test
     */
    public function testFakeIPv4Address(): void
    {
        $this->assertParsing(
            'http://333.123.32.123/',
            'http', null, '333.123.32.123', null, '/', null, null
        );
    }

    /**
     * @test
     */
    public function testIPv6Address(): void
    {
        $this->assertParsing(
            'http://[2001:db8::7]/c=GB?objectClass?one',
            'http', null, '[2001:db8::7]', null, '/c=GB', 'objectClass?one', null
        );
    }

    /**
     * @test
     */
    public function testInternationalizedDomainName(): void
    {
        $this->assertParsing(
            "http://t\xC5\xABdali\xC5\x86.lv",
            'http', null, "t\xC5\xABdali\xC5\x86.lv", null, '', null, null
        );
    }

    /**
     * @test
     */
    public function testInvalidPort(): void
    {
        $this->assertParsing(
            'http://example.com:foobar',
            'http', null, 'example.com', null, '', null, null
        );
    }

    /**
     * @test
     */
    public function testPathAbsolute(): void
    {
        $this->assertParsing(
            'http:/this/is/path',
            'http', null, null, null, '/this/is/path', null, null
        );
    }

    /**
     * @test
     */
    public function testPathRootless(): void
    {
        // this should not be used but is allowed
        $this->assertParsing(
            'http:this/is/path',
            'http', null, null, null, 'this/is/path', null, null
        );
    }

    /**
     * @test
     */
    public function testPathEmpty(): void
    {
        $this->assertParsing(
            'http:',
            'http', null, null, null, '', null, null
        );
    }

    /**
     * @test
     */
    public function testRelativeURI(): void
    {
        $this->assertParsing(
            '/a/b',
            null, null, null, null, '/a/b', null, null
        );
    }

    /**
     * @test
     */
    public function testMalformedTag(): void
    {
        $this->assertParsing(
            'http://www.example.com/>',
            'http', null, 'www.example.com', null, '/', null, null
        );
    }

    /**
     * @test
     */
    public function testEmpty(): void
    {
        $this->assertParsing(
            '',
            null, null, null, null, '', null, null
        );
    }

    /**
     * @test
     */
    public function testEmbeddedColon(): void
    {
        $this->assertParsing(
            '{:test:}',
            null, null, null, null, '{:test:}', null, null
        );
    }
}
