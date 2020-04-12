<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier_URIDefinition;
use HTMLPurifier_URIFilter;
use Mockery;

/**
 * Class URIDefinitionTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class URIDefinitionTest extends UriTestCase
{
    /**
     * @test
     */
    public function test_filter(): void
    {
        $def = new HTMLPurifier_URIDefinition();

        $def->addFilter($this->createFilterMock(), $this->config);
        $def->addFilter($this->createFilterMock(), $this->config);

        $uri = $this->createURI('test');
        static::assertTrue($def->filter($uri, $this->config, $this->context));
    }

    /**
     * @test
     */
    public function test_filter_earlyAbortIfFail(): void
    {
        $def = new HTMLPurifier_URIDefinition();
        $def->addFilter($this->createFilterMock(true, false), $this->config);
        $def->addFilter($this->createFilterMock(false), $this->config); // never called

        $uri = $this->createURI('test');
        static::assertFalse($def->filter($uri, $this->config, $this->context));
    }

    /**
     * @test
     */
    public function test_setupMemberVariables_collisionPrecedenceIsHostBaseScheme(): void
    {
        $this->config->set('URI.Host', $host = 'example.com');
        $this->config->set('URI.Base', $base = 'http://sub.example.com/foo/bar.html');
        $this->config->set('URI.DefaultScheme', 'ftp');

        $def = new HTMLPurifier_URIDefinition();
        $def->setup($this->config);

        static::assertEquals($def->host, $host);
        static::assertEquals($def->base, $this->createURI($base));
        static::assertEquals('http', $def->defaultScheme); // not ftp!
    }

    /**
     * @test
     */
    public function test_setupMemberVariables_onlyScheme(): void
    {
        $this->config->set('URI.DefaultScheme', 'ftp');
        $def = new HTMLPurifier_URIDefinition();
        $def->setup($this->config);

        static::assertEquals('ftp', $def->defaultScheme);
    }

    /**
     * @test
     */
    public function test_setupMemberVariables_onlyBase(): void
    {
        $this->config->set('URI.Base', 'http://sub.example.com/foo/bar.html');
        $def = new HTMLPurifier_URIDefinition();
        $def->setup($this->config);

        static::assertEquals('sub.example.com', $def->host);
    }

    /**
     * @param bool $expect
     * @param bool $result
     * @param bool $post
     * @param bool $setup
     *
     * @return \Mockery\MockInterface|\Mockery\LegacyMockInterface
     */
    protected function createFilterMock(
        bool $expect = true,
        bool $result = true,
        bool $post = false,
        bool $setup = true
    ) {
        static $i = 0;

        $mock = Mockery::mock(HTMLPurifier_URIFilter::class);

        if ($expect) {
            $mock->expects()
                ->filter(Mockery::any(), $this->config, $this->context)
                ->once()
                ->andReturn($result);
        } else {
            $mock->expects()
                 ->filter(Mockery::any(), $this->config, $this->context)
                 ->never()
                 ->andReturn($result);
        }

        $mock->expects()
            ->prepare($this->config)
            ->once()
            ->andReturn($setup);

        $mock->name = $i++;
        $mock->post = $post;

        return $mock;
    }
}
