<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\Token\Start;
use HTMLPurifier\URIFilter\Munge;
use function function_exists;

/**
 * Class MungeTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class MungeTest extends TestCase
{
    /**
     * @var Munge
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new Munge();
    }

    /**
     * @param string|null $uri
     *
     * @throws \HTMLPurifier\Exception
     */
    private function setMunge(?string $uri = 'http://www.google.com/url?q=%s'): void
    {
        $this->config->set('URI.Munge', $uri);
    }

    /**
     * @param string|null $key
     *
     * @return void
     * @throws \HTMLPurifier\Exception
     */
    protected function setSecureMunge(?string $key = 'secret'): void
    {
        if (!function_exists('hash_hmac')) {
            $this->markTestSkipped('hash_hmac does not exist.');
        }

        $this->setMunge('/redirect.php?url=%s&checksum=%t');
        $this->config->set('URI.MungeSecretKey', $key);
    }

    /**
     * @test
     */
    public function testMunge(): void
    {
        $this->setMunge();
        $this->assertFiltering(
            'http://www.example.com/',
            'http://www.google.com/url?q=http%3A%2F%2Fwww.example.com%2F'
        );
    }

    /**
     * @test
     */
    public function testMungeReplaceTagName(): void
    {
        $this->setMunge('/r?tagname=%n&url=%s');
        $token = new Start('a');
        $this->context->register('CurrentToken', $token);
        $this->assertFiltering('http://google.com', '/r?tagname=a&url=http%3A%2F%2Fgoogle.com');
    }

    /**
     * @test
     */
    public function testMungeReplaceAttribute(): void
    {
        $this->setMunge('/r?attr=%m&url=%s');
        $attr = 'href';
        $this->context->register('CurrentAttr', $attr);
        $this->assertFiltering('http://google.com', '/r?attr=href&url=http%3A%2F%2Fgoogle.com');
    }

    /**
     * @test
     */
    public function testMungeReplaceResource(): void
    {
        $this->setMunge('/r?embeds=%r&url=%s');
        $embeds = false;
        $this->context->register('EmbeddedURI', $embeds);
        $this->assertFiltering('http://google.com', '/r?embeds=&url=http%3A%2F%2Fgoogle.com');
    }

    /**
     * @test
     */
    public function testMungeReplaceCSSProperty(): void
    {
        $this->setMunge('/r?property=%p&url=%s');
        $property = 'background';
        $this->context->register('CurrentCSSProperty', $property);
        $this->assertFiltering('http://google.com', '/r?property=background&url=http%3A%2F%2Fgoogle.com');
    }

    /**
     * @test
     */
    public function testIgnoreEmbedded(): void
    {
        $this->setMunge();
        $embeds = true;
        $this->context->register('EmbeddedURI', $embeds);
        $this->assertFiltering('http://example.com');
    }

    /**
     * @test
     */
    public function testProcessEmbedded(): void
    {
        $this->setMunge();
        $this->config->set('URI.MungeResources', true);
        $embeds = true;
        $this->context->register('EmbeddedURI', $embeds);
        $this->assertFiltering(
            'http://www.example.com/',
            'http://www.google.com/url?q=http%3A%2F%2Fwww.example.com%2F'
        );
    }

    /**
     * @test
     */
    public function testPreserveRelative(): void
    {
        $this->setMunge();
        $this->assertFiltering('index.html');
    }

    /**
     * @test
     */
    public function testMungeIgnoreUnknownSchemes(): void
    {
        $this->setMunge();
        $this->assertFiltering('javascript:foobar();', true);
    }

    /**
     * @test
     */
    public function testSecureMungePreserve(): void
    {
        $this->setSecureMunge();
        $this->assertFiltering('/local');
    }

    /**
     * @test
     */
    public function testSecureMungePreserveEmbedded(): void
    {
        $this->setSecureMunge();
        $embedded = true;
        $this->context->register('EmbeddedURI', $embedded);
        $this->assertFiltering('http://google.com');
    }

    /**
     * @test
     */
    public function testSecureMungeStandard(): void
    {
        $this->setSecureMunge();
        $this->assertFiltering(
            'http://google.com',
            '/redirect.php?url=http%3A%2F%2Fgoogle.com&checksum=46267a796aca0ea5839f24c4c97ad2648373a4eca31b1c0d1fa7c7ff26798f79'
        );
    }

    /**
     * @test
     */
    public function testSecureMungeIgnoreUnknownSchemes(): void
    {
        // This should be integration tested as well to be false
        $this->setSecureMunge();
        $this->assertFiltering('javascript:', true);
    }

    /**
     * @test
     */
    public function testSecureMungeIgnoreUnbrowsableSchemes(): void
    {
        $this->setSecureMunge();
        $this->assertFiltering('news:', true);
    }

    /**
     * @test
     */
    public function testSecureMungeToDirectory(): void
    {
        $this->setSecureMunge();
        $this->setMunge('/links/%s/%t');
        $this->assertFiltering(
            'http://google.com',
            '/links/http%3A%2F%2Fgoogle.com/46267a796aca0ea5839f24c4c97ad2648373a4eca31b1c0d1fa7c7ff26798f79'
        );
    }

    /**
     * @test
     */
    public function testMungeIgnoreSameDomain(): void
    {
        $this->setMunge('http://example.com/%s');
        $this->assertFiltering('http://example.com/foobar');
    }

    /**
     * @test
     */
    public function testMungeIgnoreSameDomainInsecureToSecure(): void
    {
        $this->setMunge('http://example.com/%s');
        $this->assertFiltering('https://example.com/foobar');
    }

    /**
     * @test
     */
    public function testMungeIgnoreSameDomainSecureToSecure(): void
    {
        $this->config->set('URI.Base', 'https://example.com');
        $this->setMunge('http://example.com/%s');
        $this->assertFiltering('https://example.com/foobar');
    }

    /**
     * @test
     */
    public function testMungeSameDomainSecureToInsecure(): void
    {
        $this->config->set('URI.Base', 'https://example.com');
        $this->setMunge('/%s');
        $this->assertFiltering('http://example.com/foobar', '/http%3A%2F%2Fexample.com%2Ffoobar');
    }

    /**
     * @test
     */
    public function testMungeIgnoresSourceHost(): void
    {
        $this->config->set('URI.Host', 'foo.example.com');
        $this->setMunge('http://example.com/%s');
        $this->assertFiltering('http://foo.example.com/bar');
    }
}
