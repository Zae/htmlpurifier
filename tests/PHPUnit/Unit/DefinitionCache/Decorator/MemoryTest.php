<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache\Decorator;

use HTMLPurifier\DefinitionCache\Decorator\Memory;
use Mockery;

/**
 * Class MemoryTest
 *
 * @package HTMLPurifier\Tests\Unit\DefinitionCache\Decorator
 */
class MemoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->cache = new Memory();
        
        parent::setUp();
    }

    /**
     * @test
     */
    public function testGet(): void
    {
        $this->mock->expects()
            ->get($this->config)
            ->once()
            ->andReturn($this->def);

        static::assertEquals($this->def, $this->cache->get($this->config));
        static::assertEquals($this->def, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testSet(): void
    {
        $this->setupMockForSuccess('set');

        static::assertEquals(true, $this->cache->set($this->def, $this->config));
        static::assertEquals($this->def, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testSetFailure(): void
    {
        $this->setupMockForFailure('set');

        static::assertEquals(false, $this->cache->set($this->def, $this->config));
        $this->cache->get($this->config);
    }

    /**
     * @test
     */
    public function testReplace(): void
    {
        $this->setupMockForSuccess('replace');

        static::assertEquals(true, $this->cache->replace($this->def, $this->config));
        static::assertEquals($this->def, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testReplaceFailure(): void
    {
        $this->setupMockForFailure('replace');

        static::assertEquals(false, $this->cache->replace($this->def, $this->config));
        $this->cache->get($this->config);
    }

    /**
     * @test
     */
    public function testAdd(): void
    {
        $this->setupMockForSuccess('add');

        static::assertEquals(true, $this->cache->add($this->def, $this->config));
        static::assertEquals($this->def, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testAddFailure(): void
    {
        $this->setupMockForFailure('add');

        static::assertEquals(false, $this->cache->add($this->def, $this->config));
        $this->cache->get($this->config);
    }

    /**
     * @param mixed ...$ops
     */
    private function setupMockForSuccess(...$ops): void
    {
        foreach ($ops as $op) {
            $this->mock->expects()
               ->{$op}($this->def, $this->config)
               ->andReturn(true);
        }

        $this->mock->expects()
            ->get(Mockery::any())
            ->never();
    }

    /**
     * @param mixed ...$ops
     */
    public function setupMockForFailure(...$ops): void
    {
        foreach ($ops as $op) {
            $this->mock->expects()
                       ->{$op}($this->def, $this->config)
                       ->andReturn(false);
        }

        $this->mock->expects()
                   ->get(Mockery::any())
                   ->times(1);
    }
}
