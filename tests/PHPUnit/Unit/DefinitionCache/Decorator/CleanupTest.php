<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\DefinitionCache\Decorator;

use HTMLPurifier_DefinitionCache_Decorator_Cleanup;

/**
 * Class CleanupTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class CleanupTest extends TestCase
{
    public function setUp(): void
    {
        $this->cache = new HTMLPurifier_DefinitionCache_Decorator_Cleanup();

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

        $this->mock->expects()
            ->cleanup()
            ->times(0);

        static::assertEquals($this->def, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testGetFailure(): void
    {
        $this->mock->expects()
            ->get($this->config)
            ->once()
            ->andReturn(false);

        $this->mock->expects()
            ->cleanup($this->config)
            ->once()
            ->andReturn(false);

        static::assertEquals(false, $this->cache->get($this->config));
    }

    /**
     * @test
     */
    public function testSet(): void
    {
        $this->setupMockForSuccess('set');

        static::assertEquals(true, $this->cache->set($this->def, $this->config));
    }

    /**
     * @test
     */
    public function testReplace(): void
    {
        $this->setupMockForSuccess('replace');

        static::assertEquals(true, $this->cache->replace($this->def, $this->config));
    }

    /**
     * @test
     */
    public function testAdd(): void
    {
        $this->setupMockForSuccess('add');

        static::assertEquals(true, $this->cache->add($this->def, $this->config));
    }

    /**
     * @param $op
     */
    private function setupMockForSuccess($op): void
    {
        $this->mock->expects()
            ->{$op}($this->def, $this->config)
            ->once()
            ->andReturns(true);

        $this->mock->expects()
            ->cleanup($this->config)
            ->times(0);
    }

    /**
     * @param $op
     */
    private function setupMockForFailure($op): void
    {
        $this->mock->expects()
            ->{$op}($this->def, $this->config)
            ->once()
            ->andReturn(false);

        $this->mock->expects()
                   ->cleanup($this->config)
                   ->once();
    }
}
