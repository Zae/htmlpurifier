<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\URIFilter\MakeAbsolute;

/**
 * Class MakeAbsoluteTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class MakeAbsoluteTest extends TestCase
{
    /**
     * @var MakeAbsolute
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = new MakeAbsolute();
        $this->setBase();
    }

    /**
     * @param string|null $base
     *
     * @throws \HTMLPurifier\Exception
     */
    private function setBase(?string $base = 'http://example.com/foo/bar.html?q=s#frag'): void
    {
        $this->config->set('URI.Base', $base);
    }

    // corresponding to RFC 2396

    /**
     * @test
     */
    public function testPreserveAbsolute(): void
    {
        $this->assertFiltering('http://example.com/foo.html');
    }

    /**
     * @test
     */
    public function testFilterBlank(): void
    {
        $this->assertFiltering('', 'http://example.com/foo/bar.html?q=s');
    }

    /**
     * @test
     */
    public function testFilterEmptyPath(): void
    {
        $this->assertFiltering('?q=s#frag', 'http://example.com/foo/bar.html?q=s#frag');
    }

    /**
     * @test
     */
    public function testPreserveAltScheme(): void
    {
        $this->assertFiltering('mailto:bob@example.com');
    }

    /**
     * @test
     */
    public function testPreserveAltSchemeWithTel(): void
    {
        $this->assertFiltering('tel:+15555555555');
    }

    /**
     * @test
     */
    public function testFilterIgnoreHTTPSpecialCase(): void
    {
        $this->assertFiltering('http:/', 'http://example.com/');
    }

    /**
     * @test
     */
    public function testFilterAbsolutePath(): void
    {
        $this->assertFiltering('/foo.txt', 'http://example.com/foo.txt');
    }

    /**
     * @test
     */
    public function testFilterRelativePath(): void
    {
        $this->assertFiltering('baz.txt', 'http://example.com/foo/baz.txt');
    }

    /**
     * @test
     */
    public function testFilterRelativePathWithInternalDot(): void
    {
        $this->assertFiltering('./baz.txt', 'http://example.com/foo/baz.txt');
    }

    /**
     * @test
     */
    public function testFilterRelativePathWithEndingDot(): void
    {
        $this->assertFiltering('baz/.', 'http://example.com/foo/baz/');
    }

    /**
     * @test
     */
    public function testFilterRelativePathDot(): void
    {
        $this->assertFiltering('.', 'http://example.com/foo/');
    }

    /**
     * @test
     */
    public function testFilterRelativePathMultiDot(): void
    {
        $this->assertFiltering('././foo/./bar/.././baz', 'http://example.com/foo/foo/baz');
    }

    /**
     * @test
     */
    public function testFilterAbsolutePathWithDot(): void
    {
        $this->assertFiltering('/./foo', 'http://example.com/foo');
    }

    /**
     * @test
     */
    public function testFilterAbsolutePathWithMultiDot(): void
    {
        $this->assertFiltering('/./foo/../bar/.', 'http://example.com/bar/');
    }

    /**
     * @test
     */
    public function testFilterRelativePathWithInternalDotDot(): void
    {
        $this->assertFiltering('../baz.txt', 'http://example.com/baz.txt');
    }

    /**
     * @test
     */
    public function testFilterRelativePathWithEndingDotDot(): void
    {
        $this->assertFiltering('..', 'http://example.com/');
    }

    /**
     * @test
     */
    public function testFilterRelativePathTooManyDotDots(): void
    {
        $this->assertFiltering('../../', 'http://example.com/');
    }

    /**
     * @test
     */
    public function testFilterAppendingQueryAndFragment(): void
    {
        $this->assertFiltering('/foo.php?q=s#frag', 'http://example.com/foo.php?q=s#frag');
    }

    // edge cases below

    /**
     * @test
     */
    public function testFilterAbsolutePathBase(): void
    {
        $this->setBase('/foo/baz.txt');
        $this->assertFiltering('test.php', '/foo/test.php');
    }

    /**
     * @test
     */
    public function testFilterAbsolutePathBaseDirectory(): void
    {
        $this->setBase('/foo/');
        $this->assertFiltering('test.php', '/foo/test.php');
    }

    /**
     * @test
     */
    public function testFilterAbsolutePathBaseBelow(): void
    {
        $this->setBase('/foo/baz.txt');
        $this->assertFiltering('../../test.php', '/test.php');
    }

    /**
     * @test
     */
    public function testFilterRelativePathBase(): void
    {
        $this->setBase('foo/baz.html');
        $this->assertFiltering('foo.php', 'foo/foo.php');
    }

    /**
     * @test
     */
    public function testFilterRelativePathBaseBelow(): void
    {
        $this->setBase('../baz.html');
        $this->assertFiltering('test/strike.html', '../test/strike.html');
    }

    /**
     * @test
     */
    public function testFilterRelativePathBaseWithAbsoluteURI(): void
    {
        $this->setBase('../baz.html');
        $this->assertFiltering('/test/strike.html');
    }

    /**
     * @test
     */
    public function testFilterRelativePathBaseWithDot(): void
    {
        $this->setBase('../baz.html');
        $this->assertFiltering('.', '../');
    }

    /**
     * @test
     */
    public function testRemoveJavaScriptWithEmbeddedLink(): void
    {
        // credits: NykO18
        $this->setBase('http://www.example.com/');
        $this->assertFiltering('javascript: window.location = \'http://www.example.com\';', false);
    }

    // miscellaneous

    /**
     * @test
     */
    public function testFilterDomainWithNoSlash(): void
    {
        $this->setBase('http://example.com');
        $this->assertFiltering('foo', 'http://example.com/foo');
    }

    // error case

    /**
     * @test
     */
    public function testErrorNoBase(): void
    {
        $this->setBase(null);
        $this->expectError();
        $this->expectErrorMessage('URI.MakeAbsolute is being ignored due to lack of value for URI.Base configuration');
        $this->assertFiltering('foo/bar.txt');
    }
}
