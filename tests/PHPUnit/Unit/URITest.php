<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_URI;
use HTMLPurifier_URIParser;
use HTMLPurifier_URIScheme;
use HTMLPurifier_URISchemeRegistry;
use Mockery;

/**
 * Class URITest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class URITest extends UriTestCase
{
    protected $oldRegistry;

    /**
     * @test
     */
    public function test_construct(): void
    {
        $uri1 = new HTMLPurifier_URI('HTTP', 'bob', 'example.com', 23, '/foo', 'bar=2', 'slash');
        $uri2 = new HTMLPurifier_URI('http', 'bob', 'example.com',  23,  '/foo', 'bar=2', 'slash');

        static::assertEquals($uri1, $uri2);
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_getSchemeObj(): void
    {
        $scheme_mock = $this->setUpSchemeMock('http');

        $uri = $this->createURI('http:');
        $scheme_obj = $uri->getSchemeObj($this->config, $this->context);

        static::assertEquals($scheme_mock, $scheme_obj);
        $this->tearDownSchemeRegistryMock();
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_getSchemeObj_invalidScheme(): void
    {
        $this->setUpNoValidSchemes();

        $uri = $this->createURI('http:');
        $result = $uri->getSchemeObj($this->config, $this->context);

        static::assertEquals(false, $result);
        $this->tearDownSchemeRegistryMock();
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_getSchemaObj_defaultScheme(): void
    {
        $scheme = 'foobar';

        $scheme_mock = $this->setUpSchemeMock($scheme);
        $this->config->set('URI.DefaultScheme', $scheme);

        $uri = $this->createURI('hmm');
        $scheme_obj = $uri->getSchemeObj($this->config, $this->context);

        static::assertEquals($scheme_obj, $scheme_mock);
        $this->tearDownSchemeRegistryMock();
    }

    /**
     * @test
     * @throws \HTMLPurifier_Exception
     */
    public function test_getSchemaObj_invalidDefaultScheme(): void
    {
        $this->setUpNoValidSchemes();
        $this->config->set('URI.DefaultScheme', 'foobar');

        $uri = $this->createURI('hmm');

        $this->expectError();
        $this->expectErrorMessage('Default scheme object "foobar" was not readable');

        $result = $uri->getSchemeObj($this->config, $this->context);

        static::assertEquals(false, $result);
        $this->tearDownSchemeRegistryMock();
    }

    /**
     * @test
     */
    public function test_toString_full(): void
    {
        $this->assertToString(
            'http://bob@example.com:300/foo?bar=baz#fragment',
            'http', 'bob', 'example.com', 300, '/foo', 'bar=baz', 'fragment'
        );
    }

    /**
     * @test
     */
    public function test_toString_scheme(): void
    {
        $this->assertToString(
            'http:',
            'http', null, null, null, '', null, null
        );
    }

    /**
     * @test
     */
    public function test_toString_authority(): void
    {
        $this->assertToString(
            '//bob@example.com:8080',
            null, 'bob', 'example.com', 8080, '', null, null
        );
    }

    /**
     * @test
     */
    public function test_toString_path(): void
    {
        $this->assertToString(
            '/path/to',
            null, null, null, null, '/path/to', null, null
        );
    }

    /**
     * @test
     */
    public function test_toString_query(): void
    {
        $this->assertToString(
            '?q=string',
            null, null, null, null, '', 'q=string', null
        );
    }

    /**
     * @test
     */
    public function test_toString_fragment(): void
    {
        $this->assertToString(
            '#fragment',
            null, null, null, null, '', null, 'fragment'
        );
    }

    /**
     * @test
     */
    public function test_validate_overlongPort(): void
    {
        $this->assertValidation('http://example.com:65536', 'http://example.com');
    }

    /**
     * @test
     */
    public function test_validate_zeroPort(): void
    {
        $this->assertValidation('http://example.com:00', 'http://example.com');
    }

    /**
     * @test
     */
    public function test_validate_invalidHostThatLooksLikeIPv6(): void
    {
        $this->assertValidation('http://[2001:0db8:85z3:08d3:1319:8a2e:0370:7334]', '');
    }

    /**
     * @test
     */
    public function test_validate_removeRedundantScheme(): void
    {
        $this->assertValidation('http:foo:/:', 'foo%3A/:');
    }

    /**
     * @test
     */
    public function test_validate_username(): void
    {
        $this->assertValidation("http://user\xE3\x91\x94:@foo.com", 'http://user%E3%91%94:@foo.com');
    }

    /**
     * @test
     */
    public function test_validate_path_abempty(): void
    {
        $this->assertValidation("http://host/\xE3\x91\x94:", 'http://host/%E3%91%94:');
    }

    /**
     * @test
     */
    public function test_validate_path_absolute(): void
    {
        $this->assertValidation("/\xE3\x91\x94:", '/%E3%91%94:');
    }

    /**
     * @test
     */
    public function test_validate_path_rootless(): void
    {
        $this->assertValidation("mailto:\xE3\x91\x94:", 'mailto:%E3%91%94:');
    }

    /**
     * @test
     */
    public function test_validate_path_noscheme(): void
    {
        $this->assertValidation("\xE3\x91\x94", '%E3%91%94');
    }

    /**
     * @test
     */
    public function test_validate_query(): void
    {
        $this->assertValidation("?/\xE3\x91\x94", '?/%E3%91%94');
    }

    /**
     * @test
     */
    public function test_validate_fragment(): void
    {
        $this->assertValidation("#/\xE3\x91\x94", '#/%E3%91%94');
    }

    /**
     * @test
     */
    public function test_validate_path_empty(): void
    {
        $this->assertValidation('http://google.com');
    }

    /**
     * Generates a URI object from the corresponding string
     *
     * @param string $uri
     *
     * @return bool|\HTMLPurifier_URI
     */
    protected function createURI(string $uri)
    {
        $parser = new HTMLPurifier_URIParser();
        return $parser->parse($uri);
    }

    /**
     * @return mixed
     */
    protected function &setUpSchemeRegistryMock() {
        $this->oldRegistry = HTMLPurifier_URISchemeRegistry::instance();

        $registry = HTMLPurifier_URISchemeRegistry::instance(
            Mockery::mock(HTMLPurifier_URISchemeRegistry::class)
        );

        return $registry;
    }

    /**
     * @param $name
     *
     * @return HTMLPurifier_URIScheme|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function setUpSchemeMock($name): HTMLPurifier_URIScheme
    {
        $registry = $this->setUpSchemeRegistryMock();
        $scheme_mock = Mockery::mock(HTMLPurifier_URIScheme::class);

        $registry->expects()
            ->getScheme($name, $this->config, $this->context)
            ->once()
            ->andReturn($scheme_mock);

        return $scheme_mock;
    }

    protected function setUpNoValidSchemes(): void
    {
        $registry = $this->setUpSchemeRegistryMock();
        $registry->expects()
            ->getScheme(Mockery::any(), $this->config, $this->context)
            ->andReturn(false);
    }

    protected function tearDownSchemeRegistryMock(): void
    {
        HTMLPurifier_URISchemeRegistry::instance($this->oldRegistry);
    }

    /**
     * @param $expect_uri
     * @param $scheme
     * @param $userinfo
     * @param $host
     * @param $port
     * @param $path
     * @param $query
     * @param $fragment
     */
    protected function assertToString($expect_uri, $scheme, $userinfo, $host, $port, $path, $query, $fragment): void
    {
        $uri = new HTMLPurifier_URI($scheme, $userinfo, $host, $port, $path, $query, $fragment);

        $string = $uri->toString();
        static::assertEquals($string, $expect_uri);
    }

    /**
     * @param      $uri
     * @param bool|string $expect_uri
     *
     * @throws \HTMLPurifier_Exception
     */
    protected function assertValidation($uri, $expect_uri = true): void
    {
        if ($expect_uri === true) {
            $expect_uri = $uri;
        }

        $uri = $this->createURI($uri);
        $result = $uri->validate($this->config, $this->context);

        if ($expect_uri === false) {
            static::assertFalse($result);
        } else {
            static::assertTrue($result);
            static::assertEquals($expect_uri, $uri->toString());
        }
    }
}
