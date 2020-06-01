<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\URIFilter\DisableExternal;

/**
 * Class DisableExternalTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class DisableExternalTest extends TestCase
{
    /**
     * @var DisableExternal
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DisableExternal();
    }

    /**
     * @test
     */
    public function testRemoveExternal(): void
    {
        $this->assertFiltering(
            'http://example.com',
            false
        );
    }

    /**
     * @test
     */
    public function testPreserveInternal(): void
    {
        $this->assertFiltering(
            '/foo/bar'
        );
    }

    /**
     * @test
     */
    public function testPreserveOurHost(): void
    {
        $this->config->set('URI.Host', 'example.com');
        $this->assertFiltering(
            'http://example.com'
        );
    }

    /**
     * @test
     */
    public function testPreserveOurSubdomain(): void
    {
        $this->config->set('URI.Host', 'example.com');
        $this->assertFiltering(
            'http://www.example.com'
        );
    }

    /**
     * @test
     */
    public function testRemoveSuperdomain(): void
    {
        $this->config->set('URI.Host', 'www.example.com');
        $this->assertFiltering(
            'http://example.com',
            false
        );
    }

    /**
     * @test
     */
    public function testBaseAsHost(): void
    {
        $this->config->set('URI.Base', 'http://www.example.com/foo/bar');
        $this->assertFiltering(
            'http://www.example.com/baz'
        );
    }
}
