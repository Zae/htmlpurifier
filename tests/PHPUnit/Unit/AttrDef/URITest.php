<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef\URI;
use HTMLPurifier\DefinitionCache;
use HTMLPurifier\DefinitionCacheFactory;
use HTMLPurifier\URIDefinition;
use HTMLPurifier\URIParser;
use Mockery;

/**
 * Class URITest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class URITest extends TestCase
{
    protected function setUp(): void
    {
        $this->def = new URI();

        parent::setUp();
    }

    /**
     * @test
     */
    public function testIntegration(): void
    {
        $this->assertDef('http://www.google.com/');
        $this->assertDef('http:', '');
        $this->assertDef('http:/foo', '/foo');
        $this->assertDef('javascript:bad_stuff();', false);
        $this->assertDef('ftp://www.example.com/');
        $this->assertDef('news:rec.alt');
        $this->assertDef('nntp://news.example.com/324234');
        $this->assertDef('mailto:bob@example.com');
        $this->assertDef('tel:+15555555555');
    }

    /**
     * @test
     */
    public function testIntegrationWithPercentEncoder(): void
    {
        $this->assertDef(
            'http://www.example.com/%56%fc%GJ%5%FC',
            'http://www.example.com/V%FC%25GJ%255%FC'
        );
    }

    /**
     * @test
     */
    public function testPercentEncoding(): void
    {
        $this->assertDef(
            'http:colon:mercenary',
            'colon%3Amercenary'
        );
    }

    /**
     * @test
     */
    public function testPercentEncodingPreserve(): void
    {
        $this->assertDef(
            'http://www.example.com/abcABC123-_.!~*()\''
        );
    }

    /**
     * @test
     */
    public function testEmbeds(): void
    {
        $this->def = new URI(true);
        $this->assertDef('http://sub.example.com/alas?foo=asd');
        $this->assertDef('mailto:foo@example.com', false);
    }

    /**
     * @test
     */
    public function testConfigMunge(): void
    {
        $this->config->set('URI.Munge', 'http://www.google.com/url?q=%s');
        $this->assertDef(
            'http://www.example.com/',
            'http://www.google.com/url?q=http%3A%2F%2Fwww.example.com%2F'
        );
        $this->assertDef('index.html');
        $this->assertDef('javascript:foobar();', false);
    }

    /**
     * @test
     */
    public function testDefaultSchemeRemovedInBlank(): void
    {
        $this->assertDef('http:', '');
    }

    /**
     * @test
     */
    public function testDefaultSchemeRemovedInRelativeURI(): void
    {
        $this->assertDef('http:/foo/bar', '/foo/bar');
    }

    /**
     * @test
     */
    public function testDefaultSchemeNotRemovedInAbsoluteURI(): void
    {
        $this->assertDef('http://example.com/foo/bar');
    }

    /**
     * @test
     */
    public function testDefaultSchemeNull(): void
    {
        $this->config->set('URI.DefaultScheme', null);
        $this->assertDef('foo', false);
    }

    /**
     * @test
     */
    public function testAltSchemeNotRemoved(): void
    {
        $this->assertDef('mailto:this-looks-like-a-path@example.com');
    }

    /**
     * @test
     */
    public function testResolveNullSchemeAmbiguity(): void
    {
        $this->assertDef('///foo', '/foo');
    }

    /**
     * @test
     */
    public function testResolveNullSchemeDoubleAmbiguity(): void
    {
        $this->config->set('URI.Host', 'example.com');
        $this->assertDef('////foo', '//example.com//foo');
    }

    /**
     * @test
     */
    public function testURIDefinitionValidation(): void
    {
        $parser = new URIParser();
        $uri = $parser->parse('http://example.com');
        $this->config->set('URI.DefinitionID', 'HTMLPurifier_AttrDef_URITest1->testURIDefinitionValidation');

        $uri_def = Mockery::mock(URIDefinition::class);

        $uri_def->shouldReceive('filter')
            ->withArgs(static function($a) use ($uri) {
                // We are comparing with == here, because $a is not the same instance of
                // the object we passed in, but the properties should be the same.
                return $a == $uri;
            })
            ->once()
            ->andReturn(true);

        $uri_def->shouldReceive('postFilter')
            ->withArgs(static function($a) use ($uri) {
                // We are comparing with == here, because $a is not the same instance of
                // the object we passed in, but the properties should be the same.
                return $a == $uri;
            })
            ->once()
            ->andReturn(true);

        $uri_def->setup = true;

        // Since definitions are no longer passed by reference, we need
        // to muck around with the cache to insert our mock. This is
        // technically a little bad, since the cache shouldn't change
        // behavior, but I don't feel too good about letting users
        // overload entire definitions.

        $cache_mock = Mockery::mock(DefinitionCache::class);
        $cache_mock->expects()
            ->get($this->config)
            ->andReturn($uri_def);

        $factory_mock = Mockery::mock(DefinitionCacheFactory::class);

        $old = DefinitionCacheFactory::instance();
        DefinitionCacheFactory::instance($factory_mock);

        $factory_mock->expects()
            ->create('URI', $this->config)
            ->andReturns($cache_mock);

        $this->assertDef('http://example.com');

        DefinitionCacheFactory::instance($old);
    }

    /**
     * @test
     */
    public function testMake(): void
    {
        $factory = new URI();
        $def = $factory->make('');
        $def2 = new URI();
        static::assertEquals($def, $def2);

        $def = $factory->make('embedded');
        $def2 = new URI(true);
        static::assertEquals($def, $def2);
    }
}
